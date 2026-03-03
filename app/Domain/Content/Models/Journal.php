<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = ['content_item_id', 'issn', 'volume', 'issue', 'doi', 'journal_name', 'peer_reviewed', 'authors'];

    protected $casts = ['authors' => 'array', 'peer_reviewed' => 'boolean'];

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }
}
