<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClubMaster;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Spatie Roles
        $thRole = Role::firstOrCreate(['name' => 'TH']);
        $presidentRole = Role::firstOrCreate(['name' => 'President']);
        $secretaryRole = Role::firstOrCreate(['name' => 'Secretary']);
        $memberRole = Role::firstOrCreate(['name' => 'Member']);

        // 2. Create the primary Technical Head user
        $thUser = User::firstOrCreate(
            ['email' => 'th@club.com'],
            [
                'name' => 'Technical Head',
                'phone' => '1234567890',
                'password' => bcrypt('password'),
                'status' => 'active',
                'profile' => null,
            ]
        );
        $thUser->assignRole($thRole);

        // 3. Create the Club Master record
        ClubMaster::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Bhimchak Sunrise Club',
                'logo' => 'bsc_logo.jpeg',
                'address' => 'Bhimchak, Kolkata, West Bengal',
                'estd' => '2020',
            ]
        );

        // 4. Create default setting keys
        Setting::firstOrCreate(
            ['key' => 'maintenance_mode'],
            [
                'value' => '0',
                'updated_by' => $thUser->id,
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'maintenance_message'],
            [
                'value' => 'Site is currently undergoing scheduled maintenance. Please check back later.',
                'updated_by' => $thUser->id,
            ]
        );
    }
}
