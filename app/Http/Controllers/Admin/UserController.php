<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(15);
        return response()->json($users);
    }

    public function show(int $id)
    {
        $user = User::with(['patient', 'doctor'])->findOrFail($id);
        return response()->json($user);
    }
}


