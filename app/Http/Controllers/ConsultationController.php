<?php

namespace App\Http\Controllers;

use App\Models\ConsultationProvider;
use App\Services\ConsultationAccessService;
use App\Services\ConsultationChatService;
use App\Services\ConsultationWhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationWhatsAppService $whatsapp,
        private ConsultationAccessService $access,
        private ConsultationChatService $chat,
    ) {}

    public function index(): View
    {
        $categories = $this->categoriesForDisplay();

        return view('consultation.index', [
            'categories' => $categories,
            'otherDoctors' => collect($categories)
                ->filter(fn (array $cat) => ! ($cat['active'] ?? false)
                    && ($cat['primary'] ?? false)
                    && empty($cat['parent_key'])
                    && ($cat['key'] ?? '') !== 'perawat')
                ->values()
                ->all(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function categoriesForDisplay(): array
    {
        return collect(config('consultation.categories', []))
            ->map(function (array $cat) {
                $key = (string) ($cat['key'] ?? '');
                $configActive = (bool) ($cat['active'] ?? false);
                $hasProviders = $key !== ''
                    && ConsultationProvider::tableReady()
                    && ConsultationProvider::query()
                        ->where('category_key', $key)
                        ->where('active', true)
                        ->exists();

                $cat['active'] = $configActive || $hasProviders;

                return $cat;
            })
            ->values()
            ->all();
    }

    public function category(string $category): View
    {
        $meta = $this->whatsapp->categoryMeta($category);
        abort_if($meta === null, 404);

        $providers = $this->whatsapp->providersForCategory($category, $this->access);

        if ($providers !== []) {
            return view('consultation.providers', [
                'categoryKey' => $category,
                'category' => $meta,
                'providers' => $providers,
                'sessionHours' => $this->access->sessionHours(),
            ]);
        }

        $subcategories = $this->subCategoriesFor($category);

        if ($subcategories !== []) {
            return view('consultation.specialty-hub', [
                'categoryKey' => $category,
                'category' => $meta,
                'subcategories' => $subcategories,
            ]);
        }

        abort(404);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function subCategoriesFor(string $parentKey): array
    {
        return collect(config('consultation.categories', []))
            ->filter(fn (array $cat) => ($cat['parent_key'] ?? null) === $parentKey)
            ->map(function (array $cat) {
                $key = (string) ($cat['key'] ?? '');
                $configActive = (bool) ($cat['active'] ?? false);
                $hasProviders = $key !== ''
                    && ConsultationProvider::tableReady()
                    && ConsultationProvider::query()
                        ->where('category_key', $key)
                        ->where('active', true)
                        ->exists();

                $cat['active'] = $configActive || $hasProviders;

                return $cat;
            })
            ->values()
            ->all();
    }

    public function checkout(string $provider): View
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        $user = auth()->user();
        $price = $this->access->priceFor($provider);
        $categoryKey = (string) ($profile['category'] ?? $provider);
        $accessState = $this->resolveCheckoutAccessState($user, $provider);

        $openPayModal = request()->boolean('mulai')
            && in_array($accessState, ['awaiting_payment', 'rejected'], true);

        return view('consultation.checkout', [
            'providerKey' => $provider,
            'provider' => $profile,
            'categoryKey' => $categoryKey,
            'price' => $price,
            'priceLabel' => $this->access->formatRupiah($price),
            'sessionHours' => $this->access->sessionHours(),
            'accessState' => $accessState,
            'chatUrl' => route('consultation.chat', $provider),
            'statusUrl' => route('consultation.payment.status', $provider),
            'paymentUrl' => route('consultation.payment', $provider),
            'openPayModal' => $openPayModal,
            'whatsappDirectUrl' => $accessState === 'active'
                ? $this->whatsapp->buildLiveStartUrl($provider, $user)
                : null,
            'whatsappPreview' => $accessState === 'active'
                ? $this->whatsapp->buildLiveStartMessage($provider, $user)
                : null,
            'whatsappDisplayNumber' => $accessState === 'active'
                ? $this->whatsapp->displayNumber($provider)
                : null,
        ]);
    }

    /**
     * @return 'active'|'pending_verification'|'awaiting_payment'|'rejected'
     */
    private function resolveCheckoutAccessState(\App\Models\User $user, string $provider): string
    {
        if ($this->access->hasAccess($user, $provider)) {
            return 'active';
        }

        $pending = $this->access->pendingOrder($user, $provider);

        if ($pending?->payment_proof) {
            return 'pending_verification';
        }

        $latest = $this->access->latestOrder($user, $provider);

        if ($latest?->isRejected()) {
            return 'rejected';
        }

        return 'awaiting_payment';
    }

    public function redeemVoucher(Request $request, string $provider): RedirectResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:50'],
        ]);

        $this->access->redeemVoucher(auth()->user(), $provider, $validated['voucher_code']);

        $user = auth()->user();
        $order = $this->access->latestOrder($user, $provider);

        if ($this->access->hasAccess($user, $provider)) {
            return redirect()
                ->route('consultation.checkout', $provider)
                ->with('status', 'Voucher diterapkan. Sesi chat aktif — mulai konsultasi.');
        }

        return redirect()
            ->route('consultation.payment', $provider)
            ->with('status', 'Voucher diterapkan. Bayar sisa '.$this->access->formatRupiah($order?->total_paid ?? 0).' via DANA.');
    }

    public function pay(string $provider): RedirectResponse
    {
        return redirect()->route('consultation.payment', $provider);
    }

    public function payment(string $provider): View|RedirectResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        $user = auth()->user();
        $pending = $this->access->pendingOrder($user, $provider);

        if ($pending?->payment_proof) {
            return redirect()->route('consultation.payment.status', $provider);
        }

        $price = $this->access->priceFor($provider);
        $dueAmount = $this->access->dueAmountForPayment($user, $provider);
        $orderId = $pending?->reference_code ?? 'CK-'.strtoupper($provider).'-'.now()->format('ymdHis');

        return view('consultation.payment-dana', [
            'providerKey' => $provider,
            'provider' => $profile,
            'price' => $price,
            'dueAmount' => $dueAmount,
            'priceLabel' => $this->access->formatRupiah($dueAmount),
            'originalPriceLabel' => $this->access->formatRupiah($price),
            'voucherCode' => $pending?->voucher_code,
            'discountLabel' => $pending?->discount_amount ? $this->access->formatRupiah($pending->discount_amount) : null,
            'merchantName' => config('consultation.dana.merchant_name', 'Chatbot Keperawatan'),
            'merchantPhone' => config('consultation.dana.merchant_phone', '085645527751'),
            'orderId' => $orderId,
        ]);
    }

    public function paymentStatus(string $provider): View|RedirectResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        $user = auth()->user();
        $order = $this->access->latestOrder($user, $provider);

        if ($this->access->hasAccess($user, $provider)) {
            return view('consultation.payment-status', [
                'providerKey' => $provider,
                'provider' => $profile,
                'order' => $order,
                'state' => 'approved',
                'priceLabel' => $this->access->formatRupiah($this->access->priceFor($provider)),
                'chatUrl' => route('consultation.chat', $provider),
                'sessionHours' => $this->access->sessionHours(),
                'whatsappPreview' => $this->whatsapp->buildLiveStartMessage($provider, $user),
                'whatsappDisplayNumber' => $this->whatsapp->displayNumber($provider),
            ]);
        }

        if ($order?->isPending()) {
            return view('consultation.payment-status', [
                'providerKey' => $provider,
                'provider' => $profile,
                'order' => $order,
                'state' => 'pending',
                'priceLabel' => $this->access->formatRupiah($order->total_paid),
                'chatUrl' => route('consultation.chat', $provider),
                'pollUrl' => route('consultation.payment.poll', $provider),
            ]);
        }

        if ($order?->isRejected()) {
            return view('consultation.payment-status', [
                'providerKey' => $provider,
                'provider' => $profile,
                'order' => $order,
                'state' => 'rejected',
                'priceLabel' => $this->access->formatRupiah($order->total_paid),
                'chatUrl' => route('consultation.chat', $provider),
                'retryUrl' => route('consultation.checkout', ['provider' => $provider, 'mulai' => 1]),
            ]);
        }

        return redirect()->route('consultation.payment', $provider);
    }

    public function paymentPoll(string $provider): JsonResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        $user = auth()->user();
        $order = $this->access->latestOrder($user, $provider);

        if ($this->access->hasAccess($user, $provider)) {
            return response()->json([
                'status' => 'approved',
                'redirect_url' => route('consultation.payment.status', $provider),
            ]);
        }

        if ($order?->isPending()) {
            return response()->json(['status' => 'pending']);
        }

        if ($order?->isRejected()) {
            return response()->json([
                'status' => 'rejected',
                'redirect_url' => route('consultation.payment.status', $provider),
            ]);
        }

        return response()->json([
            'status' => 'none',
            'redirect_url' => route('consultation.payment', $provider),
        ]);
    }

    public function payDana(Request $request, string $provider): RedirectResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        $request->validate([
            'dana_phone' => ['required', 'string', 'min:5', 'max:120'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'payment_confirmed' => ['accepted'],
            'order_reference' => ['required', 'string', 'max:40'],
        ], [
            'dana_phone.required' => 'Identitas pengirim wajib diisi.',
            'dana_phone.min' => 'Identitas pengirim minimal 5 karakter.',
            'dana_phone.max' => 'Identitas pengirim maksimal 120 karakter.',
            'payment_proof.required' => 'Upload bukti transfer wajib.',
            'payment_proof.file' => 'Bukti transfer harus berupa file.',
            'payment_proof.mimes' => 'Bukti transfer harus JPG, PNG, WEBP, atau PDF.',
            'payment_proof.max' => 'Ukuran bukti transfer maksimal 5 MB.',
            'payment_confirmed.accepted' => 'Centang konfirmasi bahwa Anda sudah transfer.',
            'order_reference.required' => 'Referensi pesanan tidak valid. Muat ulang halaman.',
        ]);

        $user = auth()->user();
        $proofPath = $request->file('payment_proof')->store('consultation-payment-proofs', 'public');

        $this->access->submitDanaPayment(
            $user,
            $provider,
            $request->input('dana_phone'),
            $request->input('order_reference'),
            $proofPath,
        );

        return redirect()
            ->route('consultation.payment.status', $provider)
            ->with('status', 'Konfirmasi pembayaran terkirim. Menunggu verifikasi admin.');
    }

    public function chat(string $provider): View|RedirectResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        if ($redirect = $this->access->redirectIfNoAccess(auth()->user(), $provider)) {
            return $redirect;
        }

        $user = auth()->user();
        $order = $this->access->activeOrder($user, $provider);
        abort_if($order === null, 404);

        $this->chat->ensureWelcomeMessage($order);
        $this->chat->markReadByUser($order);

        return view('consultation.chat', [
            'providerKey' => $provider,
            'provider' => $profile,
            'order' => $order,
            'sessionHours' => $this->access->sessionHours(),
            'messagesUrl' => route('consultation.chat.messages', $provider),
            'sendUrl' => route('consultation.send', $provider),
            'checkoutUrl' => route('consultation.checkout', $provider),
            'expiresAt' => $order->expires_at?->toIso8601String(),
            'initialMessages' => $this->chat->messagesPayload($order),
        ]);
    }

    public function messages(Request $request, string $provider): JsonResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        if (! $this->access->hasAccess(auth()->user(), $provider)) {
            return response()->json(['message' => 'Akses chat belum aktif.'], 402);
        }

        $order = $this->access->activeOrder(auth()->user(), $provider);
        abort_if($order === null, 404);

        $afterId = $request->integer('after', 0) ?: null;

        if ($afterId === null) {
            $this->chat->markReadByUser($order);
        }

        return response()->json([
            'messages' => $this->chat->messagesPayload($order, $afterId),
            'expires_at' => $order->expires_at?->toIso8601String(),
        ]);
    }

    public function send(Request $request, string $provider): JsonResponse
    {
        $profile = $this->whatsapp->provider($provider);
        abort_if($profile === null, 404);

        if (! $this->access->hasAccess(auth()->user(), $provider)) {
            $pending = $this->access->pendingOrder(auth()->user(), $provider);

            return response()->json([
                'message' => $pending
                    ? 'Pembayaran menunggu verifikasi admin.'
                    : 'Akses chat belum aktif. Silakan bayar atau gunakan voucher 100%.',
                'checkout_url' => route('consultation.checkout', $provider),
                'status_url' => $pending ? route('consultation.payment.status', $provider) : null,
            ], 402);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->chat->sendUserMessage(
            auth()->user(),
            $provider,
            $validated['message'],
        );

        return response()->json([
            'message' => $message->toChatArray(),
            'reply' => 'Pesan terkirim. Tenaga kesehatan akan membalas segera di chat ini.',
        ]);
    }

    private function redirectToWhatsApp(string $provider, $user, string $via = 'pembayaran'): RedirectResponse
    {
        $url = $this->whatsapp->buildLiveStartUrl($provider, $user, $via);

        if ($url === '') {
            return redirect()
                ->route('consultation.checkout', $provider)
                ->with('upload_error', 'Nomor WhatsApp perawat belum dikonfigurasi.');
        }

        return redirect()->away($url);
    }
}
