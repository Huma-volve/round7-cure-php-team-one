<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DoctorPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'doctor']);
    }

    protected function createDoctorUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $specialty = Specialty::firstOrCreate(['name' => 'Cardiology']);

        Doctor::factory()->create([
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
        ]);

        return $user;
    }

    public function test_doctor_can_access_dashboard(): void
    {
        $user = $this->createDoctorUser();

        $response = $this->actingAs($user)
            ->get('/admin/doctor/dashboard');

        $response->assertStatus(200);
    }

    public function test_non_doctor_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/doctor/dashboard')
            ->assertStatus(403);
    }

    public function test_doctor_can_update_schedule(): void
    {
        $user = $this->createDoctorUser();

        $payload = [
            'schedule' => [
                'monday' => ['start' => '09:00', 'end' => '17:00'],
            ],
        ];

        $response = $this->actingAs($user)
            ->post('/admin/doctor/schedule', $payload);

        $response->assertRedirect(route('doctor.schedule.edit'));

        $this->assertEquals(
            ['monday' => ['09:00-17:00']],
            $user->fresh()->doctor->availability_json
        );
    }
}

