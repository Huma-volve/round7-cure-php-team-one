<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات
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

        // إنشاء الأدوار وربطها بالصلاحيات

        // Admin Role - كل الصلاحيات
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $adminRole->givePermissionTo(Permission::where('guard_name', 'api')->get());

        // Doctor Role
        $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'api']);
        $doctorRole->syncPermissions(['chat_with_patient', 'manage_bookings']);

        // Patient Role - بدون صلاحيات إدارية
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'api']);

        // إنشاء يوزر أدمن افتراضي 
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'mobile' => '0550000000',
            ]
        );

        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
    }
}
