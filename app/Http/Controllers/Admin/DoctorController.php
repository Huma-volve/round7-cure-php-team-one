<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Doctor::withTrashed()
            ->with([
                'user' => function ($q) {
                    $q->withTrashed();
                },
                'specialty'
            ]);

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->whereHas('user', function ($w) use ($q) {
                $w->withTrashed()
                    ->where(function ($sub) use ($q) {
                        $sub->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $doctors = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        $specialties = Specialty::all();
        return view('admin.doctors.create', compact('specialties'));
    }

    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // Assign doctor role
        $doctorRoleApi = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'api']);
        $doctorRoleWeb = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        
        $user->assignRole($doctorRoleApi);
        
        DB::table('model_has_roles')->insertOrIgnore([
            'role_id' => $doctorRoleWeb->id,
            'model_type' => get_class($user),
            'model_id' => $user->id,
        ]);

        // Create doctor
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $request->specialty_id,
            'license_number' => $request->license_number,
            'clinic_address' => $request->clinic_address,
            'consultation' => $request->consultation ?? 'clinic',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'session_price' => $request->session_price,
            'availability_json' => $request->availability_json ?? [],
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.doctors.show', $doctor->id)
            ->with('success', 'تم إضافة الطبيب بنجاح');
    }

    public function show(int $id): View
    {
        $doctor = Doctor::with(['user', 'specialty', 'bookings.patient.user', 'reviews'])
            ->withCount(['bookings', 'reviews'])
            ->findOrFail($id);
        return view('admin.doctors.show', compact('doctor'));
    }

    public function edit(int $id): View
    {
        $doctor = Doctor::with(['user', 'specialty'])->findOrFail($id);
        $specialties = Specialty::all();
        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    public function update(UpdateDoctorRequest $request, int $id): RedirectResponse
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        
        // Update user
        $doctor->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);

        // Update doctor
        $doctor->update([
            'specialty_id' => $request->specialty_id,
            'license_number' => $request->license_number,
            'clinic_address' => $request->clinic_address,
            'consultation' => $request->consultation ?? $doctor->consultation,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'session_price' => $request->session_price,
            'availability_json' => $request->availability_json ?? $doctor->availability_json,
        ]);

        return redirect()
            ->route('admin.doctors.show', $doctor->id)
            ->with('success', 'تم تحديث بيانات الطبيب بنجاح');
    }

    public function destroy(int $id): RedirectResponse
    {
        $doctor = Doctor::findOrFail($id);
        
        // Delete user (this will cascade delete doctor due to foreign key)
        $doctor->delete();


        $doctor->user?->delete();

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'تم حذف الطبيب بنجاح');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $doctor = Doctor::findOrFail($id);
        
        $newStatus = $doctor->status === 'active' ? 'inactive' : 'active';
        $doctor->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'تم تفعيل الطبيب بنجاح' : 'تم إيقاف الطبيب بنجاح';

        return redirect()
            ->route('admin.doctors.show', $doctor->id)
            ->with('success', $message);
    }
}
