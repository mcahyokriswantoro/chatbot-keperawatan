<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'pharmacy_name',
        'price',
        'stock',
        'description',
        'photo',
        'active',
    ];

    public function pharmacyLabel(): string
    {
        return $this->pharmacy_name ?: 'Semua Apotek';
    }

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'active' => 'boolean',
    ];

    public function photoUrl(): string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::disk('public')->url($this->photo);
        }

        // Default medicine icon / photo
        return asset('images/unggulan_obat.png');
    }
}
