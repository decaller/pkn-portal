<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::updateOrCreate(
            ["email" => "harridiilmantovid@gmail.com"],
            [
                "name" => "Admin",
                "phone_number" => "081100000001",
                "password" => Hash::make("cemara153"), // Change this later!
                "is_super_admin" => true,
            ],
        );

        $pkn = Organization::firstOrCreate(
            ["slug" => "pkn"],
            [
                "name" => "PKN",
                "admin_user_id" => $admin->id,
            ],
        );

        $pkn->users()->syncWithoutDetaching([
            $admin->id => ["role" => "admin"],
        ]);

        $ybitAdmin = User::updateOrCreate(
            ["email" => "admin.ybit@example.com"],
            [
                "name" => "YBIT Admin",
                "phone_number" => "081100000002",
                "password" => Hash::make("password123"),
                "is_super_admin" => false,
            ],
        );

        $ybitMember = User::updateOrCreate(
            ["email" => "member.ybit@example.com"],
            [
                "name" => "YBIT Member",
                "phone_number" => "081100000003",
                "password" => Hash::make("password123"),
                "is_super_admin" => false,
            ],
        );

        $ybit = Organization::updateOrCreate(
            ["slug" => "ybit"],
            [
                "name" => "Yayasan Bina Insan Taqwa",
                "admin_user_id" => $ybitAdmin->id,
            ],
        );

        $ybit->users()->syncWithoutDetaching([
            $ybitAdmin->id => ["role" => "admin"],
            $ybitMember->id => ["role" => "member"],
        ]);

        Event::updateOrCreate(
            ["slug" => "pkn-national-conference-2026"],
            [
                "title" => "PKN National Conference 2026",
                "description" => "Annual national offline conference by PKN.",
                "event_date" => now()->addMonths(3)->toDateString(),
                "is_published" => true,
                "allow_registration" => true,
            ],
        );

        Event::updateOrCreate(
            ["slug" => "pkn-regional-workshop-2026"],
            [
                "title" => "PKN Regional Workshop 2026",
                "description" =>
                    "Regional offline workshop and networking session.",
                "event_date" => now()->addMonths(6)->toDateString(),
                "is_published" => true,
                "allow_registration" => true,
            ],
        );
    }
}
