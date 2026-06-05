<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '1112223334',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('1112223334', $user->phone);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
                'phone' => $user->phone ?? '1234567890',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_page_displays_total_donated(): void
    {
        $user = User::factory()->create();

        // Create approved credit transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => 500.00,
            'type' => 'credit',
            'method' => 'cash',
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        // Create pending credit transaction (should not sum)
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => 250.00,
            'type' => 'credit',
            'method' => 'bank',
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        // Create approved debit transaction (should not sum)
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'type' => 'debit',
            'method' => 'cash',
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertSee('Total Donated');
        $response->assertSee('500.00');
    }

    public function test_user_can_fetch_paginated_transactions_json(): void
    {
        $user = User::factory()->create();

        // Create a transaction
        $transaction = \App\Models\Transaction::create([
            'transaction_id' => '123456789',
            'user_id' => $user->id,
            'amount' => 1500.00,
            'type' => 'credit',
            'method' => 'bank',
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('profile.transactions'));

        $response->assertOk();
        $response->assertJsonStructure([
            'transactions',
            'has_more',
            'current_page',
        ]);
        
        $data = $response->json();
        $this->assertCount(1, $data['transactions']);
        $this->assertEquals('1,500.00', $data['transactions'][0]['amount']);
        $this->assertEquals('Bank', $data['transactions'][0]['method']);
    }
}
