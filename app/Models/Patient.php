<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class Patient extends Model

{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birthdate',
        'gender',
        'medical_notes',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
