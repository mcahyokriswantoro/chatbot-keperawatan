@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow"><p class="text-xs text-slate-500">Pengguna</p><p class="text-2xl font-bold">{{ $userCount }}</p></div>
        <div class="bg-white rounded-xl p-4 shadow"><p class="text-xs text-slate-500">Skrining</p><p class="text-2xl font-bold">{{ $screeningCount }}</p></div>
        <div class="bg-white rounded-xl p-4 shadow"><p class="text-xs text-slate-500">Darurat</p><p class="text-2xl font-bold text-rose-600">{{ $emergencyCount }}</p></div>
        <div class="bg-white rounded-xl p-4 shadow"><p class="text-xs text-slate-500">Artikel</p><p class="text-2xl font-bold">{{ $articleCount }}</p></div>
    </div>

    <h2 class="text-lg font-bold mb-3">Skrining Terbaru</h2>
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Pengguna</th>
                    <th class="px-4 py-2">Risiko</th>
                    <th class="px-4 py-2">Darurat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentScreenings as $s)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $s->user?->name ?? 'Tamu' }}</td>
                        <td class="px-4 py-2 capitalize">{{ $s->risk_level }}</td>
                        <td class="px-4 py-2">{{ $s->is_emergency ? 'Ya' : 'Tidak' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
