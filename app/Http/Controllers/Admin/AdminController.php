<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ArabicSearchHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Get only users with 'admin' role (Spatie)
        $query = User::role('admin');

        // Search
        if ($request->filled('q')) {

            $q = $request->string('q');
            $normalizedQ = ArabicSearchHelper::normalizeArabicText($q);

            $query->where(function ($w) use ($q, $normalizedQ) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")

                  // normalized search
                  ->orWhereRaw("
                        REPLACE(REPLACE(REPLACE(REPLACE(name, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه') 
                        LIKE ?
                    ", ["%{$normalizedQ}%"])

                  ->orWhereRaw("
                        REPLACE(REPLACE(REPLACE(REPLACE(email, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه') 
                        LIKE ?
                    ", ["%{$normalizedQ}%"]);
            });
        }

        $admins = $query->orderByDesc('id')->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }
}
