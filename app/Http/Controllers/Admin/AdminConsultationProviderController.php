<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminConsultationProviderController extends Controller
{
    public function index(): View
    {
        if (! ConsultationProvider::tableReady()) {
            return view('admin.consultations.providers.index', [
                'providers' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'categories' => $this->categoryOptions(),
                'setupRequired' => true,
            ]);
        }

        $providers = ConsultationProvider::query()
            ->orderBy('category_key')
            ->orderBy('sort_order')
            ->orderBy('short_name')
            ->paginate(20);

        return view('admin.consultations.providers.index', [
            'providers' => $providers,
            'categories' => $this->categoryOptions(),
            'setupRequired' => false,
        ]);
    }

    public function create(): View
    {
        return view('admin.consultations.providers.form', [
            'provider' => new ConsultationProvider([
                'active' => true,
                'rating_percent' => 100,
                'sort_order' => 0,
                'icon' => '👩‍⚕️',
            ]),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProvider($request);

        $key = filled($validated['key'] ?? null)
            ? Str::slug($validated['key'])
            : ConsultationProvider::generateKey($validated['short_name'], $validated['category_key']);

        $provider = ConsultationProvider::create([
            'key' => $key,
            'category_key' => $validated['category_key'],
            'active' => $request->boolean('active', true),
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'title' => $validated['title'] ?? null,
            'specialty' => $validated['specialty'] ?? null,
            'credential' => $validated['credential'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'rating_percent' => $validated['rating_percent'] ?? null,
            'price' => $validated['price'] ?? null,
            'icon' => '👩‍⚕️',
            'whatsapp' => $validated['whatsapp'],
            'whatsapp_intl' => ConsultationProvider::normalizeWhatsappIntl($validated['whatsapp'], $validated['whatsapp_intl'] ?? null),
            'greeting' => $validated['greeting'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'photo' => $this->storePhoto($request),
        ]);

        return redirect()
            ->route('admin.consultations.providers.edit', $provider)
            ->with('status', 'Tenaga kesehatan '.$provider->short_name.' berhasil ditambahkan.');
    }

    public function edit(ConsultationProvider $provider): View
    {
        return view('admin.consultations.providers.form', [
            'provider' => $provider,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, ConsultationProvider $provider): RedirectResponse
    {
        $validated = $this->validateProvider($request, $provider);

        $photoPath = $this->resolveUpdatedPhotoPath($request, $provider);

        $provider->update([
            'key' => Str::slug($validated['key']),
            'category_key' => $validated['category_key'],
            'active' => $request->boolean('active'),
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'title' => $validated['title'] ?? null,
            'specialty' => $validated['specialty'] ?? null,
            'credential' => $validated['credential'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'rating_percent' => $validated['rating_percent'] ?? null,
            'price' => $validated['price'] ?? null,
            'whatsapp' => $validated['whatsapp'],
            'whatsapp_intl' => ConsultationProvider::normalizeWhatsappIntl($validated['whatsapp'], $validated['whatsapp_intl'] ?? null),
            'greeting' => $validated['greeting'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'photo' => $photoPath,
        ]);

        return back()->with('status', 'Data '.$provider->short_name.' diperbarui.');
    }

    public function toggle(ConsultationProvider $provider): RedirectResponse
    {
        $provider->update(['active' => ! $provider->active]);

        return back()->with('status', $provider->short_name.' '.($provider->active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    public function destroy(ConsultationProvider $provider): RedirectResponse
    {
        $name = $provider->short_name;

        if ($provider->photo && ! str_starts_with($provider->photo, 'images/')) {
            Storage::disk('public')->delete($provider->photo);
        }

        $provider->delete();

        return redirect()
            ->route('admin.consultations.providers.index')
            ->with('status', 'Tenaga kesehatan '.$name.' dihapus.');
    }

    /**
     * @return array<int, array{key: string, label: string}>
     */
    private function categoryOptions(): array
    {
        return collect(config('consultation.categories', []))
            ->map(fn ($cat) => [
                'key' => (string) ($cat['key'] ?? ''),
                'label' => (string) ($cat['label'] ?? ''),
            ])
            ->filter(fn ($cat) => $cat['key'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProvider(Request $request, ?ConsultationProvider $provider = null): array
    {
        return $request->validate([
            'key' => $provider
                ? ['required', 'string', 'max:60', Rule::unique('consultation_providers', 'key')->ignore($provider->id)]
                : ['nullable', 'string', 'max:60', 'unique:consultation_providers,key'],
            'category_key' => ['required', 'string', 'max:50'],
            'active' => ['sometimes', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:120'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'credential' => ['nullable', 'string', 'max:80'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'rating_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'price' => ['nullable', 'integer', 'min:0'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'whatsapp_intl' => ['nullable', 'string', 'max:20'],
            'greeting' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'remove_photo' => ['sometimes', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'short_name.required' => 'Nama tampilan wajib diisi.',
            'category_key.required' => 'Kategori wajib dipilih.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'photo.mimes' => 'Foto harus JPG, PNG, WEBP, atau GIF.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
        ]);
    }

    private function resolveUpdatedPhotoPath(Request $request, ConsultationProvider $provider): ?string
    {
        if ($request->boolean('remove_photo')) {
            $this->deleteStoredPhoto($provider->photo);

            return null;
        }

        if (! $request->hasFile('photo')) {
            return $provider->photo;
        }

        $file = $request->file('photo');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'photo' => 'Upload gagal: '.$this->uploadErrorMessage($file?->getError() ?? UPLOAD_ERR_NO_FILE),
            ]);
        }

        $this->deleteStoredPhoto($provider->photo);

        return $file->store('consultation-providers', 'public');
    }

    private function deleteStoredPhoto(?string $path): void
    {
        if (! $path || str_starts_with($path, 'images/') || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas server.',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian. Coba lagi.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file dipilih.',
            default => 'Kode error '.$code,
        };
    }

    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $file = $request->file('photo');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return null;
        }

        return $file->store('consultation-providers', 'public');
    }
}
