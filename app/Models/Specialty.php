<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Specialty extends Model
{
    use HasFactory;
        protected $fillable = ['name' , 'image'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }


// في App\Models\Specialty
public function getImageUrlAttribute()
{
    if ($this->image) {
        return asset('storage/' . $this->image);
    }
    return null;
}

    /**
     * Scope للبحث بالاسم
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}
