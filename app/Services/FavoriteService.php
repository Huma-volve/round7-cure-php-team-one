<?php

namespace App\Services;
use App\Models\User;
use App\Models\Doctor;

class FavoriteService{


     public function toggleFavorite(User $user, Doctor $doctor)
    {
        if ($this->isFavorite($user, $doctor)) {

            $user->favorites()->detach($doctor->id);
            return 'removed';
        } else {

            $user->favorites()->syncWithoutDetaching([$doctor->id]);
            return 'added';
        }
    }

    public function isFavorite(User $user, Doctor $doctor)
    {
        return $user->favorites()->where('doctor_id', $doctor->id)->exists();
    }


 public function getFavorites(User $user)
    {
        return $user->favorites()->get();
    }
}

