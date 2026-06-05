<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed manage_users permission
        \Spatie\Permission\Models\Permission::create(['name' => 'manage_users']);

        // Seed basic roles and assign permission
        $th = Role::create(['name' => 'TH']);
        $th->givePermissionTo('manage_users');

        Role::create(['name' => 'Member']);
        Role::create(['name' => 'President']);
    }

    public function test_th_can_access_create_user_page()
    {
        $th = User::factory()->create(['status' => 'active']);
        $th->assignRole('TH');

        $response = $this->actingAs($th)->get('/users/create');

        $response->assertStatus(200);
        $response->assertSee('New Member Profile Configuration');
    }

    public function test_non_th_cannot_access_create_user_page()
    {
        $member = User::factory()->create(['status' => 'active']);
        $member->assignRole('Member');

        $response = $this->actingAs($member)->get('/users/create');
        $response->assertStatus(403);
    }

    public function test_th_can_store_new_active_user()
    {
        $th = User::factory()->create(['status' => 'active']);
        $th->assignRole('TH');

        $response = $this->actingAs($th)->post('/users', [
            'name' => 'New Active User',
            'email' => 'newactive@club.com',
            'phone' => '9999999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Member',
        ]);

        $response->assertRedirect('/users');
        
        $this->assertDatabaseHas('users', [
            'name' => 'New Active User',
            'email' => 'newactive@club.com',
            'phone' => '9999999999',
            'status' => 'active',
            'created_by' => $th->id,
        ]);

        $newUser = User::where('email', 'newactive@club.com')->first();
        $this->assertTrue($newUser->hasRole('Member'));
    }

    public function test_user_directory_renders_columns_based_on_manage_users_permission()
    {
        // 1. User with manage_users permission
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('TH');

        // 2. User without manage_users permission
        $member = User::factory()->create(['status' => 'active']);
        $member->assignRole('Member');

        // Create a user in directory to render
        $targetUser = User::factory()->create(['name' => 'John Doe']);

        // Admin visit directory
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('Registered');
        $response->assertSee('Status');
        $response->assertSee('Actions');
        $response->assertDontSee('Total Donated');

        // Member visit directory
        $response = $this->actingAs($member)->get('/users');
        $response->assertStatus(200);
        $response->assertDontSee('Registered');
        $response->assertDontSee('Status');
        $response->assertDontSee('Actions');
        $response->assertSee('Total Donated');
    }
}
