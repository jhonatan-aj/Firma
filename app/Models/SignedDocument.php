<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'file_path',
        'original_path',
        'status',
        'error_message'
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
