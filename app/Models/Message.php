<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'type',
        'content',
        'file_url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ✅ العلاقات
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }
    public function receiver()
{
    return $this->belongsTo(User::class, 'receiver_id');
}
}
