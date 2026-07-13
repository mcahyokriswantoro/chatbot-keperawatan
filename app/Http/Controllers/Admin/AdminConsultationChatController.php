<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationOrder;
use App\Models\ConsultationProvider;
use App\Services\ConsultationChatService;
use App\Services\ConsultationWhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminConsultationChatController extends Controller
{
    public function __construct(
        private ConsultationChatService $chat,
        private ConsultationWhatsAppService $whatsapp,
    ) {}

    public function index(Request $request): View
    {
        $providerKey = $request->query('provider');

        // Batasi hanya untuk provider miliknya sendiri jika bukan super admin
        if ($request->user()->provider_key && ! $request->user()->isAdmin()) {
            $providerKey = $request->user()->provider_key;
        }

        $threads = $this->chat->activeThreads(is_string($providerKey) && $providerKey !== '' ? $providerKey : null)
            ->map(function (ConsultationOrder $thread) {
                $thread->message_preview = $this->chat->latestMessagePreview($thread);

                return $thread;
            });

        $providers = ConsultationProvider::tableReady()
            ? ( ($request->user()->provider_key && ! $request->user()->isAdmin())
                ? ConsultationProvider::query()->where('key', $request->user()->provider_key)->get(['key', 'short_name'])
                : ConsultationProvider::query()->orderBy('short_name')->get(['key', 'short_name']) )
            : collect();

        $unreadTotal = ($request->user()->provider_key && ! $request->user()->isAdmin())
            ? $this->chat->unreadCountForProvider($request->user()->provider_key)
            : $this->chat->unreadCountForProvider();

        return view('admin.consultations.chat.index', [
            'threads' => $threads,
            'providers' => $providers,
            'providerKey' => $providerKey,
            'unreadTotal' => $unreadTotal,
        ]);
    }

    public function show(ConsultationOrder $order): View
    {
        abort_unless($order->isActive(), 404);

        if (auth()->user()->provider_key && ! auth()->user()->isAdmin()) {
            abort_unless($order->provider_key === auth()->user()->provider_key, 403);
        }

        $order->load(['user']);
        $this->chat->markReadByProvider($order);

        $provider = $this->whatsapp->provider($order->provider_key);

        return view('admin.consultations.chat.show', [
            'order' => $order,
            'provider' => $provider,
            'messages' => $this->chat->messagesPayload($order),
            'patientName' => $order->user?->name ?? 'Pasien',
            'providerName' => $provider['short_name'] ?? $provider['name'] ?? $order->provider_key,
        ]);
    }

    public function messages(Request $request, ConsultationOrder $order): JsonResponse
    {
        abort_unless($order->isActive(), 404);

        if ($request->user()->provider_key && ! $request->user()->isAdmin()) {
            abort_unless($order->provider_key === $request->user()->provider_key, 403);
        }

        $afterId = $request->integer('after', 0) ?: null;

        if ($afterId === null) {
            $this->chat->markReadByProvider($order);
        }

        return response()->json([
            'messages' => $this->chat->messagesPayload($order, $afterId),
            'expires_at' => $order->expires_at?->toIso8601String(),
        ]);
    }

    public function reply(Request $request, ConsultationOrder $order): RedirectResponse|JsonResponse
    {
        abort_unless($order->isActive(), 404);

        if ($request->user()->provider_key && ! $request->user()->isAdmin()) {
            abort_unless($order->provider_key === $request->user()->provider_key, 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->chat->sendProviderReply(
            $request->user(),
            $order,
            $validated['message'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message->toChatArray(),
            ]);
        }

        return back()->with('status', 'Balasan terkirim ke pasien.');
    }
}
