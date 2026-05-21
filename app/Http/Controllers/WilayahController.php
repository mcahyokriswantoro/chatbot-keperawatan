<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function children(Request $request): JsonResponse
    {
        $parentKode = $request->query('parent');

        abort_unless(is_string($parentKode) && $parentKode !== '', 404);
        abort_unless(Wilayah::where('kode', $parentKode)->exists(), 404);

        $children = Wilayah::childrenOf($parentKode)
            ->orderBy('nama')
            ->get(['kode', 'nama']);

        return response()->json($children);
    }
}
