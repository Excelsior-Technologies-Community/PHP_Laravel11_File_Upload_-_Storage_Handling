<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'details',
        'images',
        'primary_image',
        'size',
        'color',
        'category',
        'price',
        'status',
        'tag_ids',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'images' => 'array',
        'tag_ids' => 'array',
    ];
}