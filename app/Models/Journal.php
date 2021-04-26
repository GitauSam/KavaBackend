<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'user',
        'image_uri'
    ];

    public function avatar() {
        return $this->hasOne(JournalPostImage::class);
    }
}
