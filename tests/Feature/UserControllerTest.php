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
        $response->assertSee('<th class="px-6 py-4">Status</th>', false);
        $response->assertSee('Actions');
        $response->assertSee('Total Donated');

        // Member visit directory
        $response = $this->actingAs($member)->get('/users');
        $response->assertStatus(200);
        $response->assertDontSee('Registered');
        $response->assertDontSee('<th class="px-6 py-4">Status</th>', false);
        $response->assertDontSee('Actions');
        $response->assertSee('Total Donated');
    }

    public function test_member_directory_calculates_and_displays_monthly_status()
    {
        $member = User::factory()->create(['status' => 'active', 'name' => 'Alice Member']);
        $member->assignRole('Member');

        $currentMonth = now()->month;

        // Transaction in current month: credit 500
        \App\Models\Transaction::create([
            'user_id' => $member->id,
            'amount' => 500.00,
            'type' => 'credit',
            'status' => 'approved',
        ]);

        // Transaction in current month: debit 200
        \App\Models\Transaction::create([
            'user_id' => $member->id,
            'amount' => 200.00,
            'type' => 'debit',
            'status' => 'approved',
        ]);

        // Transaction in another month: credit 1000
        $t = \App\Models\Transaction::create([
            'user_id' => $member->id,
            'amount' => 1000.00,
            'type' => 'credit',
            'status' => 'approved',
        ]);
        $t->created_at = now()->month == 1 ? now()->month(2) : now()->month(1);
        $t->save();

        $otherMonth = $t->created_at->month;

        // Visit index (default current month)
        $response = $this->actingAs($member)->get('/users');
        $response->assertStatus(200);
        // Balance: +₹300.00
        $response->assertSee('+₹300.00');

        // Visit index for other month
        $response = $this->actingAs($member)->get('/users?month=' . $otherMonth);
        $response->assertStatus(200);
        // Balance: +₹1,000.00
        $response->assertSee('+₹1,000.00');
    }
}

