<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePatientRequest;
use App\Http\Requests\Admin\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Patient::with(['user']);

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->whereHas('user', function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->string('gender'));
        }

        $patients = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('admin.patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // Assign patient role
        $patientRoleApi = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'api']);
        $patientRoleWeb = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        
        $user->assignRole($patientRoleApi);
        
        DB::table('model_has_roles')->insertOrIgnore([
            'role_id' => $patientRoleWeb->id,
            'model_type' => get_class($user),
            'model_id' => $user->id,
        ]);

        // Create patient
        $patient = Patient::create([
            'user_id' => $user->id,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'medical_notes' => $request->medical_notes,
        ]);

        return redirect()
            ->route('admin.patients.show', $patient->id)
            ->with('success', 'تم إضافة المريض بنجاح');
    }

    public function show(int $id): View
    {
        $patient = Patient::with(['user', 'bookings.doctor.user'])
            ->withCount(['bookings'])
            ->findOrFail($id);
        
        // Get bookings for this patient
        $bookings = \App\Models\Booking::where('patient_id', $patient->id)
            ->with(['doctor.user'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.patients.show', compact('patient', 'bookings'));
    }

    public function edit(int $id): View
    {
        $patient = Patient::with(['user'])->findOrFail($id);
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, int $id): RedirectResponse
    {
        $patient = Patient::with('user')->findOrFail($id);
        
        // Update user
        $patient->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);

        // Update patient
        $patient->update([
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'medical_notes' => $request->medical_notes,
        ]);

        return redirect()
            ->route('admin.patients.show', $patient->id)
            ->with('success', 'تم تحديث بيانات المريض بنجاح');
    }

    public function destroy(int $id): RedirectResponse
    {
        $patient = Patient::findOrFail($id);
        
        // Delete user (this will cascade delete patient due to foreign key)
        $patient->user->delete();

        return redirect()
            ->route('admin.patients.index')
            ->with('success', 'تم حذف المريض بنجاح');
    }
}
