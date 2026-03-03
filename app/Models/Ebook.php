<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = ['content_item_id', 'author', 'isbn', 'publisher', 'pages', 'edition', 'file_format'];
    public $timestamps = false;
}
