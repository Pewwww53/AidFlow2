<?php

namespace Database\Seeders;

use App\Services\FirebaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirebaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firebase = app(FirebaseService::class);

        // Create admin user
        $adminUser = [
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ];

        // Create regular user
        $regularUser = [
            'name' => 'John Doe',
            'username' => 'john',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ];

        // Create welfare officer user
        $welfareUser = [
            'name' => 'Jane Smith',
            'username' => 'jane',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ];

        try {
            echo "Seeding Firebase Realtime Database...\n";

            // Add users
            $this->command->info('Adding admin user...');
            $firebase->createUser($adminUser);
            $this->command->info('✓ Admin user created');

            $this->command->info('Adding regular users...');
            $firebase->createUser($regularUser);
            $this->command->info('✓ Regular user created');

            $firebase->createUser($welfareUser);
            $this->command->info('✓ Welfare officer created');

            echo "\n✓ Firebase seeding completed successfully!\n";
            echo "Test credentials:\n";
            echo "  Username: admin\n";
            echo "  Password: password123\n";
        } catch (\Exception $e) {
            $this->command->error('Error seeding Firebase: ' . $e->getMessage());
        }
    }
}
