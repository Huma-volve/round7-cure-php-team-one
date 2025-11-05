<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message',
        'last_message_id',
        'last_message_at',
    ];

        protected $dates = ['deleted_at'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function meta()
    {
        return $this->hasMany(ChatUserMeta::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_user_meta', 'chat_id', 'user_id');
    }
}
