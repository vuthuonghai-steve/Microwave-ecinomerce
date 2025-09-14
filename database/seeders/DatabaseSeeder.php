<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed demo users
        $customer = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Hai@123'),
                'email_verified_at' => now(),
            ]
        );

        // Ensure roles
        \DB::table('users')->where('id', $customer->id)->update(['role' => 'CUSTOMER']);
        \DB::table('users')->where('id', $admin->id)->update(['role' => 'ADMIN']);

        // Seed catalog (brands, categories, products, stocks)
        $this->call(CatalogSeeder::class);
        // Seed one default address for the first user
        $this->call(AddressSeeder::class);
    }
}
