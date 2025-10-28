<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    //
    protected $fillable = [
        'user_id',
        'gender',
        'birthdate',
        'medical_notes'
    ];

    public function reviews(){
        
    }
}
