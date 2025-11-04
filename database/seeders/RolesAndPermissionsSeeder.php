<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات للـ API guard
        $permissions = [
            'manage_bookings',
            'create_doctor',
            'view_reports',
            'handle_refunds',
            'chat_with_patient',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // إنشاء الأدوار وربطها بالصلاحيات للـ API guard
        $adminRoleApi = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $adminRoleApi->givePermissionTo(Permission::where('guard_name', 'api')->get());

        $doctorRoleApi = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'api']);
        $doctorRoleApi->syncPermissions(['chat_with_patient', 'manage_bookings']);

        $patientRoleApi = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'api']);

        // إنشاء الأدوار للـ Web guard (للـ Admin Panel)
        $adminRoleWeb = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $doctorRoleWeb = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $patientRoleWeb = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);

        // إنشاء يوزر أدمن افتراضي
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'mobile' => '0550000000',
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role for API guard (default guard)
        if (!$adminUser->hasRole('admin', 'api')) {
            $adminUser->assignRole($adminRoleApi);
        }

        // Assign admin role for Web guard (for admin panel)
        // Use direct DB insert since User model has guard_name = 'api'
        if (!$adminUser->hasRole('admin', 'web')) {
            \DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $adminRoleWeb->id,
                'model_type' => get_class($adminUser),
                'model_id' => $adminUser->id,
            ]);
        }





        // إنشاء doctor أدمن افتراضي
        $doctorUser = User::firstOrCreate(
            ['email' => 'doctortest@example.com'],
            [
                'name' => 'doctor test',
                'password' => bcrypt('123456'),
                'mobile' => '01144778523',
            ]
        );

        // Assign doctor role for API guard
        if (!$doctorUser->hasRole('doctor', 'api')) {
            $doctorUser->assignRole($doctorRoleApi);
        }

        // Assign doctor role for Web guard
        if (!$doctorUser->hasRole('doctor', 'web')) {
            \DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $doctorRoleWeb->id,
                'model_type' => get_class($doctorUser),
                'model_id' => $doctorUser->id,
            ]);
        }



        // إنشاء patient أدمن افتراضي
        $patientUser = User::firstOrCreate(
            ['email' => 'patient1@example.com'],
            [
                'name' => 'patient test',
                'password' => Hash::make('password'),
                'mobile' => '01144778598',
            ]
        );

        // Assign patient role for API guard
        if (!$patientUser->hasRole('patient', 'api')) {
            $patientUser->assignRole($patientRoleApi);
        }

        // Assign patient role for Web guard
        if (!$patientUser->hasRole('patient', 'web')) {
            \DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $patientRoleWeb->id,
                'model_type' => get_class($patientUser),
                'model_id' => $patientUser->id,
            ]);
        }
    }
}
