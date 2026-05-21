@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Artikel Edukasi</h1>
        <a href="{{ route('admin.articles.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white">+ Tambah</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-2">Judul</th>
                    <th class="px-4 py-2">Kategori</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $article->title }}</td>
                        <td class="px-4 py-2">{{ $article->category }}</td>
                        <td class="px-4 py-2">{{ $article->is_published ? 'Publik' : 'Draft' }}</td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="text-brand-600">Edit</a>
                            <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="inline" onsubmit="return confirm('Hapus artikel?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $articles->links() }}</div>
@endsection
