<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomecarePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'icon',
        'active',
    ];

    protected $casts = [
        'price' => 'integer',
        'active' => 'boolean',
    ];
}
