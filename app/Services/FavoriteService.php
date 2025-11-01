<?php

namespace App\Services;
use App\Models\User;
use App\Models\Doctor;

class FavoriteService{


     public function toggleFavorite(User $user, Doctor $doctor)
    {
        if ($this->isFavorite($user, $doctor)) {

            $user->favorites()->detach($doctor->id);
            return [
                'status' => 'removed', // or added
                'doctor_id' => $doctor->id
                ];
        } else {

            $user->favorites()->syncWithoutDetaching([$doctor->id]);
            
            return [
                'status' => 'added', // or removed
                'doctor_id' => $doctor->id
                ];
        }
    }

    public function isFavorite(User $user, Doctor $doctor)
    {
        return $user->favorites()->where('doctor_id', $doctor->id)->exists();
    }


 public function getFavorites(User $user)
    {
        return $user->favorites()
        ->with('user:id,name,profile_photo' , 'specialty:id,name') // Eager load user details
        ->get();
    }
}

