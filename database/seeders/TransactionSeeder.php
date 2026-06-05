<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('status', 'active')->get();

        if ($users->isEmpty()) {
            $this->command->error('No active users found. Run DatabaseSeeder first.');
            return;
        }

        // Find admin users for approved_by
        $admins = User::role(['TH', 'President'])->get();
        $adminIds = $admins->pluck('id')->toArray();

        $remarks = [
            'Monthly membership dues',
            'Annual subscription fee',
            'Event sponsorship contribution',
            'Sports equipment purchase',
            'Refreshments for meeting',
            'Printing and stationery',
            'Hall booking charges',
            'Transportation expenses',
            'Prize money for tournament',
            'Charity donation collection',
            'Emergency fund contribution',
            'Club banner and flex printing',
            'Sound system rental',
            'First aid kit purchase',
            'Photography charges',
            'Decoration materials',
            'Guest speaker honorarium',
            'Trophies and medals',
            'Club T-shirt printing',
            'Internet and phone recharge',
            'Water and electricity bill',
            'Cleaning supplies',
            'Food catering for event',
            'Venue decoration',
            'Volunteer travel allowance',
            'Medical camp expenses',
            'Cultural program budget',
            'Workshop materials',
            'Library book purchase',
            'Miscellaneous club expense',
        ];

        $statuses = ['approved', 'approved', 'approved', 'pending', 'rejected']; // 60% approved, 20% pending, 20% rejected
        $types = ['credit', 'credit', 'debit']; // 66% credit, 33% debit
        $methods = ['cash', 'bank', 'bank']; // 33% cash, 66% bank

        $this->command->info('Creating 60 dummy transactions...');

        for ($i = 0; $i < 60; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $type = $types[array_rand($types)];
            $method = $methods[array_rand($methods)];
            $amount = rand(50, 5000) + (rand(0, 99) / 100);
            $remark = $remarks[array_rand($remarks)];
            $createdAt = Carbon::now()->subDays(rand(0, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $approvedAt = null;
            $approvedBy = null;
            $rejectedAt = null;
            $rejectedBy = null;

            if ($status === 'approved' && !empty($adminIds)) {
                $approvedBy = $adminIds[array_rand($adminIds)];
                $approvedAt = $createdAt->copy()->addHours(rand(1, 48));
            } elseif ($status === 'rejected' && !empty($adminIds)) {
                $rejectedBy = $adminIds[array_rand($adminIds)];
                $rejectedAt = $createdAt->copy()->addHours(rand(1, 48));
                $remark .= ' (Rejected: Insufficient documentation)';
            }

            Transaction::create([
                'user_id' => $user->id,
                'amount' => round($amount, 2),
                'type' => $type,
                'method' => $method,
                'remark' => $remark,
                'status' => $status,
                'document_url' => null,
                'approved_at' => $approvedAt,
                'approved_by' => $approvedBy,
                'rejected_at' => $rejectedAt,
                'rejected_by' => $rejectedBy,
                'created_by' => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('✅ 60 dummy transactions created successfully!');
    }
}
