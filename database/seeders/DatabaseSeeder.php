<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClubMaster;
use App\Models\Setting;
use App\Models\Notice;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Spatie Permissions
        $defaultPermissions = [
            'manage_users',
            'manage_transactions',
            'approve_transactions',
            'manage_notices',
            'manage_events',
            'manage_gallery',
            'manage_settings',
        ];

        foreach ($defaultPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // 2. Create Spatie Roles
        $thRole = Role::firstOrCreate(['name' => 'TH']);
        $presidentRole = Role::firstOrCreate(['name' => 'President']);
        $secretaryRole = Role::firstOrCreate(['name' => 'Secretary']);
        $memberRole = Role::firstOrCreate(['name' => 'Member']);

        // Sync default permissions to roles
        $thRole->syncPermissions(Permission::all());
        $presidentRole->syncPermissions(Permission::all());
        $secretaryRole->syncPermissions([
            'manage_users',
            'manage_transactions',
            'manage_notices',
            'manage_events',
            'manage_gallery',
        ]);

        // 2. Create Users
        // Technical Head
        $thUser = User::firstOrCreate(
            ['email' => 'th@club.com'],
            [
                'name' => 'Technical Head',
                'phone' => '1234567890',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        $thUser->syncRoles([$thRole]);

        // President
        $presidentUser = User::firstOrCreate(
            ['email' => 'president@club.com'],
            [
                'name' => 'Club President',
                'phone' => '9876543210',
                'password' => bcrypt('password'),
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );
        $presidentUser->syncRoles([$presidentRole]);

        // Secretary
        $secretaryUser = User::firstOrCreate(
            ['email' => 'secretary@club.com'],
            [
                'name' => 'Club Secretary',
                'phone' => '8765432109',
                'password' => bcrypt('password'),
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );
        $secretaryUser->syncRoles([$secretaryRole]);

        // Active Member 1
        $member1 = User::firstOrCreate(
            ['email' => 'member1@club.com'],
            [
                'name' => 'Rohan Sharma',
                'phone' => '7654321098',
                'password' => bcrypt('password'),
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );
        $member1->syncRoles([$memberRole]);

        // Active Member 2
        $member2 = User::firstOrCreate(
            ['email' => 'member2@club.com'],
            [
                'name' => 'Priya Patel',
                'phone' => '6543210987',
                'password' => bcrypt('password'),
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );
        $member2->syncRoles([$memberRole]);

        // Pending Member
        $pendingMember = User::firstOrCreate(
            ['email' => 'pending@club.com'],
            [
                'name' => 'Amit Kumar',
                'phone' => '5432109876',
                'password' => bcrypt('password'),
                'status' => 'pending',
                'created_by' => $thUser->id,
            ]
        );
        $pendingMember->syncRoles([$memberRole]);

        // Inactive Member
        $inactiveMember = User::firstOrCreate(
            ['email' => 'inactive@club.com'],
            [
                'name' => 'Suresh Das',
                'phone' => '4321098765',
                'password' => bcrypt('password'),
                'status' => 'inactive',
                'created_by' => $thUser->id,
            ]
        );
        $inactiveMember->syncRoles([$memberRole]);


        // 3. Create the Club Master record
        ClubMaster::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Bhimchak Sunrise Club',
                'logo' => 'uploads/logo/bsc_logo.jpeg',
                'address' => 'Bhimchak, Kolkata, West Bengal',
                'estd' => '2020',
            ]
        );

        // 4. Create default setting keys
        Setting::updateOrCreate(
            ['key' => 'maintenance_mode'],
            [
                'value' => '0',
                'updated_by' => $thUser->id,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'maintenance_message'],
            [
                'value' => 'Site is currently undergoing scheduled maintenance. Please check back later.',
                'updated_by' => $thUser->id,
            ]
        );

        // 5. Dummy Notices
        Notice::firstOrCreate(
            ['title' => 'Welcome to our new Club Portal!'],
            [
                'description' => 'We are excited to launch our new digital portal. You can now submit payment receipts, view upcoming events, check announcements, and view photos in the gallery.',
                'note' => 'Please update your profile details and picture after logging in.',
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );

        Notice::firstOrCreate(
            ['title' => 'Annual General Meeting 2026'],
            [
                'description' => 'The Annual General Meeting of Bhimchak Sunrise Club will be held in the main conference hall. All active members are requested to attend to discuss annual planning and budget reviews.',
                'note' => 'Attendance is mandatory for all office holders.',
                'status' => 'active',
                'created_by' => $presidentUser->id,
            ]
        );

        // 6. Dummy Events
        $event1 = Event::firstOrCreate(
            ['title' => 'Sunrise Sports Tournament'],
            [
                'description' => 'Our annual local cricket and football tournament involving neighboring community clubs.',
                'start_date' => Carbon::now()->addDays(2)->setHour(9)->setMinute(0),
                'end_date' => Carbon::now()->addDays(4)->setHour(18)->setMinute(0),
                'manager_id' => $secretaryUser->id,
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );

        $event2 = Event::firstOrCreate(
            ['title' => 'Weekly General Meetup'],
            [
                'description' => 'Weekly sync for members to coordinate pending club activities and sit together.',
                'start_date' => Carbon::now()->addDays(7)->setHour(17)->setMinute(0),
                'end_date' => Carbon::now()->addDays(7)->setHour(19)->setMinute(0),
                'manager_id' => $presidentUser->id,
                'status' => 'active',
                'created_by' => $thUser->id,
            ]
        );

        // 7. Dummy Gallery Item (using existing logo as source doc_url)
        Gallery::firstOrCreate(
            ['title' => 'Club Inauguration Photo'],
            [
                'event_id' => $event1->id,
                'doc_url' => 'uploads/logo/bsc_logo.jpeg',
                'description' => 'A memory from the Sunrise Sports Tournament coordination meetup.',
                'created_by' => $thUser->id,
            ]
        );

        // 8. Dummy Transactions
        // Approved Dues payment (Credit)
        Transaction::firstOrCreate(
            ['remark' => 'Yearly membership dues paid via cash'],
            [
                'user_id' => $member1->id,
                'amount' => 500.00,
                'type' => 'credit',
                'method' => 'cash',
                'status' => 'approved',
                'document_url' => null,
                'approved_at' => Carbon::now()->subDays(1),
                'approved_by' => $presidentUser->id,
                'created_by' => $member1->id,
            ]
        );

        // Pending bank transfer (Credit)
        Transaction::firstOrCreate(
            ['remark' => 'Monthly club fees transfer - Bank receipt'],
            [
                'user_id' => $member2->id,
                'amount' => 1000.00,
                'type' => 'credit',
                'method' => 'bank',
                'status' => 'pending',
                'document_url' => 'uploads/logo/bsc_logo.jpeg', // using bsc_logo as mock uploaded receipt file
                'created_by' => $member2->id,
            ]
        );

        // Approved Club Expense (Debit)
        Transaction::firstOrCreate(
            ['remark' => 'Purchased stationery and notices register'],
            [
                'user_id' => $secretaryUser->id,
                'amount' => 250.00,
                'type' => 'debit',
                'method' => 'cash',
                'status' => 'approved',
                'document_url' => null,
                'approved_at' => Carbon::now()->subHours(5),
                'approved_by' => $thUser->id,
                'created_by' => $secretaryUser->id,
            ]
        );
    }
}
