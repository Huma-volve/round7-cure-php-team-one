<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--name= : User name}
                            {--email= : Email address}
                            {--password= : Password}
                            {--mobile= : Mobile number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the admin panel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Creating new Admin user...');
        $this->newLine();

        // Get user input
        $name = $this->option('name') ?: $this->ask('User name');
        $email = $this->option('email') ?: $this->ask('Email address');
        $password = $this->option('password') ?: $this->secret('Password');
        $mobile = $this->option('mobile') ?: $this->ask('Mobile number (optional)', '0000000000');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'mobile' => $mobile,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'mobile' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("❌ User with email {$email} already exists!");
            if (!$this->confirm('Do you want to update it and add admin role?', false)) {
                return 1;
            }
            $user = User::where('email', $email)->first();
        } else {
            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'mobile' => $mobile,
                'email_verified_at' => now(),
            ]);
            $this->info("✅ User created: {$user->name}");
        }

        // Get admin roles for both guards
        $adminRoleApi = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $adminRoleWeb = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Assign admin role for API guard
        if (!$user->hasRole('admin', 'api')) {
            $user->assignRole($adminRoleApi);
            $this->info('✅ Admin role assigned for API guard');
        }

        // Assign admin role for Web guard (for admin panel)
        $webRoleExists = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('role_id', $adminRoleWeb->id)
            ->exists();
            
        if (!$webRoleExists) {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $adminRoleWeb->id,
                'model_type' => get_class($user),
                'model_id' => $user->id,
            ]);
            $this->info('✅ Admin role assigned for Web guard (Admin Panel)');
        }

        // Refresh user roles cache
        $user->load('roles');
        
        // Check roles using DB query (more reliable than hasRole with different guard)
        $apiRoleExists = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('role_id', $adminRoleApi->id)
            ->exists();
            
        $webRoleExists = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('role_id', $adminRoleWeb->id)
            ->exists();

        $this->newLine();
        $this->info('🎉 Admin user created successfully!');
        $this->table(
            ['Information', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Mobile', $user->mobile ?? 'Not set'],
                ['API Role', $apiRoleExists ? '✅ Yes' : '❌ No'],
                ['Web Role (Admin Panel)', $webRoleExists ? '✅ Yes' : '❌ No'],
            ]
        );

        $this->newLine();
        $this->info('🔗 You can now login at: http://127.0.0.1:8000/login');
        $this->info('📧 Email: ' . $user->email);
        $this->info('🔑 Password: ' . ($password ?: 'the one you entered'));

        return 0;
    }
}
