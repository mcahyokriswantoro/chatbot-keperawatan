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
        $threads = $this->chat->activeThreads(is_string($providerKey) && $providerKey !== '' ? $providerKey : null)
            ->map(function (ConsultationOrder $thread) {
                $thread->message_preview = $this->chat->latestMessagePreview($thread);

                return $thread;
            });

        $providers = ConsultationProvider::tableReady()
            ? ConsultationProvider::query()->orderBy('short_name')->get(['key', 'short_name'])
            : collect();

        return view('admin.consultations.chat.index', [
            'threads' => $threads,
            'providers' => $providers,
            'providerKey' => $providerKey,
            'unreadTotal' => $this->chat->unreadCountForProvider(),
        ]);
    }

    public function show(ConsultationOrder $order): View
    {
        abort_unless($order->isActive(), 404);

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
