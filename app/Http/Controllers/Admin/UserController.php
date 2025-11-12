<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ArabicSearchHelper;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $normalizedQ = ArabicSearchHelper::normalizeArabicText($q);
            
            $query->where(function ($w) use ($q, $normalizedQ) {
                // Search with original text
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  // Search with normalized text (handles Arabic character variations)
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(name, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه') LIKE ?", ["%{$normalizedQ}%"])
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(email, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه') LIKE ?", ["%{$normalizedQ}%"]);
            });
        }

        $users = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function show(int $id): View
    {
        $user = User::with(['patient', 'doctor'])->findOrFail($id);
        
        // Get bookings through patient or doctor
        $bookings = collect();
        if ($user->patient) {
            $bookings = \App\Models\Booking::where('patient_id', $user->patient->id)
                ->with(['doctor.user', 'patient.user', 'payment'])
                ->latest()
                ->take(10)
                ->get();
        } elseif ($user->doctor) {
            $bookings = \App\Models\Booking::where('doctor_id', $user->doctor->id)
                ->with(['doctor.user', 'patient.user', 'payment'])
                ->latest()
                ->take(10)
                ->get();
        }
        
        // Get user roles for both guards
        $apiRoles = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->where('roles.guard_name', 'api')
            ->pluck('roles.name')
            ->toArray();
            
        $webRoles = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->where('roles.guard_name', 'web')
            ->pluck('roles.name')
            ->toArray();
        
        $allRoles = Role::whereIn('guard_name', ['api', 'web'])->get()->groupBy('guard_name');
        
        return view('admin.users.show', compact('user', 'apiRoles', 'webRoles', 'allRoles', 'bookings'));
    }

    public function edit(int $id): View
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'لا يمكنك حذف حسابك الشخصي');
        }
        
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function updateRoles(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
            'guard' => 'required|in:api,web',
        ]);

        $user = User::findOrFail($id);
        $guard = $request->guard;
        
        // Get roles for the specified guard
        $roles = Role::whereIn('name', $request->roles)
            ->where('guard_name', $guard)
            ->get();
        
        // Remove all existing roles for this guard
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereIn('role_id', function($query) use ($guard) {
                $query->select('id')
                    ->from('roles')
                    ->where('guard_name', $guard);
            })
            ->delete();
        
        // Assign new roles using direct DB insert
        foreach ($roles as $role) {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $role->id,
                'model_type' => get_class($user),
                'model_id' => $user->id,
            ]);
        }
        
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'تم تحديث الأدوار بنجاح');
    }
}


