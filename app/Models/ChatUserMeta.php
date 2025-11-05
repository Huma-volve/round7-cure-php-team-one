<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatUserMeta extends Model
{
    use HasFactory;

    protected $table = 'chat_user_meta';

    protected $fillable = [
        'chat_id',
        'user_id',
        'favorite',
        'archived',
        'muted',
        'deleted_at',
        'last_read_message_id',
    ];

    protected $casts = [
        'favorite' => 'boolean',
        'archived' => 'boolean',
        'muted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
