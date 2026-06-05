<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        Permission::create(['name' => 'manage_transactions']);
        Permission::create(['name' => 'approve_transactions']);

        $thRole = Role::create(['name' => 'TH']);
        $thRole->givePermissionTo(['manage_transactions', 'approve_transactions']);

        $secRole = Role::create(['name' => 'Secretary']);
        $secRole->givePermissionTo('manage_transactions');

        $memberRole = Role::create(['name' => 'Member']);
    }

    public function test_user_with_manage_and_approve_transactions_creates_approved_transaction()
    {
        $th = User::factory()->create(['status' => 'active']);
        $th->assignRole('TH');

        $targetUser = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($th)->post(route('transactions.store'), [
            'user_id' => $targetUser->id,
            'amount' => 500.50,
            'type' => 'debit',
            'method' => 'cash',
            'remark' => 'Approved debit transaction',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success', 'Transaction recorded and approved successfully.');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $targetUser->id,
            'amount' => 500.50,
            'type' => 'debit',
            'method' => 'cash',
            'remark' => 'Approved debit transaction',
            'status' => 'approved',
            'approved_by' => $th->id,
            'created_by' => $th->id,
        ]);
    }

    public function test_user_with_manage_but_no_approve_transactions_creates_pending_transaction()
    {
        $secretary = User::factory()->create(['status' => 'active']);
        $secretary->assignRole('Secretary');

        $targetUser = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($secretary)->post(route('transactions.store'), [
            'user_id' => $targetUser->id,
            'amount' => 300.00,
            'type' => 'credit',
            'method' => 'bank',
            'remark' => 'Pending credit transaction',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success', 'Transaction submitted successfully and is pending approval.');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $targetUser->id,
            'amount' => 300.00,
            'type' => 'credit',
            'method' => 'bank',
            'remark' => 'Pending credit transaction',
            'status' => 'pending',
            'approved_by' => null,
            'created_by' => $secretary->id,
        ]);
    }

    public function test_user_without_manage_transactions_creates_pending_credit_for_self()
    {
        $member = User::factory()->create(['status' => 'active']);
        $member->assignRole('Member');

        $otherUser = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($member)->post(route('transactions.store'), [
            'user_id' => $otherUser->id, // Should be ignored
            'amount' => 150.00,
            'type' => 'debit', // Should be ignored (default to credit)
            'method' => 'cash',
            'remark' => 'Member credit transaction',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success', 'Transaction submitted successfully and is pending approval.');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $member->id, // Asserts user_id is forced to current logged in user
            'amount' => 150.00,
            'type' => 'credit', // Asserts type is forced to credit
            'method' => 'cash',
            'remark' => 'Member credit transaction',
            'status' => 'pending',
            'approved_by' => null,
            'created_by' => $member->id,
        ]);
    }

    public function test_validation_fails_for_missing_required_fields()
    {
        $member = User::factory()->create(['status' => 'active']);
        $member->assignRole('Member');

        $response = $this->actingAs($member)->post(route('transactions.store'), [
            'amount' => '',
            'method' => 'invalid-method',
        ]);

        $response->assertSessionHasErrors(['amount', 'method']);
    }

    public function test_validation_fails_when_manager_omits_required_fields()
    {
        $secretary = User::factory()->create(['status' => 'active']);
        $secretary->assignRole('Secretary');

        $response = $this->actingAs($secretary)->post(route('transactions.store'), [
            'amount' => 100.00,
            'method' => 'cash',
            'user_id' => '', // Required for managers
            'type' => '', // Required for managers
        ]);

        $response->assertSessionHasErrors(['user_id', 'type']);
    }

    public function test_user_with_manage_transactions_sees_all_transactions_via_api()
    {
        $secretary = User::factory()->create(['status' => 'active']);
        $secretary->assignRole('Secretary');

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Transaction::create([
            'user_id' => $userA->id, 'amount' => 100, 'type' => 'credit',
            'method' => 'cash', 'status' => 'pending', 'created_by' => $userA->id,
        ]);
        Transaction::create([
            'user_id' => $userB->id, 'amount' => 200, 'type' => 'credit',
            'method' => 'cash', 'status' => 'approved', 'created_by' => $userB->id,
        ]);
        Transaction::create([
            'user_id' => $userB->id, 'amount' => 300, 'type' => 'credit',
            'method' => 'cash', 'status' => 'rejected', 'created_by' => $userB->id,
        ]);

        $response = $this->actingAs($secretary)->getJson(route('transactions.load'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'transactions');
        $response->assertJsonPath('can_manage', true);
    }

    public function test_user_without_manage_transactions_sees_approved_and_own_via_api()
    {
        $member = User::factory()->create(['status' => 'active']);
        $member->assignRole('Member');

        $otherUser = User::factory()->create();

        // Own pending (should see)
        Transaction::create([
            'user_id' => $member->id, 'amount' => 111, 'type' => 'credit',
            'method' => 'cash', 'status' => 'pending', 'created_by' => $member->id,
        ]);
        // Other approved (should see)
        Transaction::create([
            'user_id' => $otherUser->id, 'amount' => 222, 'type' => 'credit',
            'method' => 'cash', 'status' => 'approved', 'created_by' => $otherUser->id,
        ]);
        // Other pending (should NOT see)
        Transaction::create([
            'user_id' => $otherUser->id, 'amount' => 333, 'type' => 'credit',
            'method' => 'cash', 'status' => 'pending', 'created_by' => $otherUser->id,
        ]);
        // Other rejected (should NOT see)
        Transaction::create([
            'user_id' => $otherUser->id, 'amount' => 444, 'type' => 'credit',
            'method' => 'cash', 'status' => 'rejected', 'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($member)->getJson(route('transactions.load'));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'transactions'); // own pending + other approved
        $response->assertJsonPath('can_manage', false);

        $amounts = collect($response->json('transactions'))->pluck('amount')->toArray();
        $this->assertContains('111.00', $amounts);
        $this->assertContains('222.00', $amounts);
        $this->assertNotContains('333.00', $amounts);
        $this->assertNotContains('444.00', $amounts);
    }

    public function test_cursor_pagination_returns_correct_batches()
    {
        $th = User::factory()->create(['status' => 'active']);
        $th->assignRole('TH');

        // Create 20 transactions
        for ($i = 1; $i <= 20; $i++) {
            Transaction::create([
                'user_id' => $th->id, 'amount' => $i * 10, 'type' => 'credit',
                'method' => 'cash', 'status' => 'approved', 'created_by' => $th->id,
            ]);
        }

        // First load: should get 15 records, has_more = true
        $response = $this->actingAs($th)->getJson(route('transactions.load'));
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'transactions');
        $response->assertJsonPath('has_more', true);

        // Get last_id from the response
        $lastId = collect($response->json('transactions'))->last()['id'];

        // Second load with last_id: should get 5 records, has_more = false
        $response2 = $this->actingAs($th)->getJson(route('transactions.load', ['last_id' => $lastId]));
        $response2->assertStatus(200);
        $response2->assertJsonCount(5, 'transactions');
        $response2->assertJsonPath('has_more', false);
    }

    public function test_index_page_renders_successfully()
    {
        $member = User::factory()->create(['status' => 'active']);
        $member->assignRole('Member');

        $response = $this->actingAs($member)->get(route('transactions.index'));
        $response->assertStatus(200);
        $response->assertSee('Transactions History');
    }
}
