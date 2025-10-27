<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
 {
    protected $fillable = [

    'user_id',
    'specialty_id',
    'license_number',
    'clinic_address',
    'latitude',
    'longitude',
    'session_price',
    'availability_json',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
{
    return $this->belongsTo(Specialty::class);
}


 public function favorites()
{
    return $this->belongsToMany(User::class, 'favorites');
}

}
