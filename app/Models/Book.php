<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'author_id',
        'title_search',
        'author_search',
        'file_path',
        'cover_path',
        'description',
        'epub_data',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'epub_data' => 'array',
        'file_size' => 'integer',
    ];

    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
