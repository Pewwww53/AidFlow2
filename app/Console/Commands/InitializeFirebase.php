<?php

namespace App\Console\Commands;

use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InitializeFirebase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:init {--test : Add test data}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Initialize Firebase Realtime Database connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $firebase = app(FirebaseService::class);

        $this->info('Testing Firebase Realtime Database connection...');

        try {
            // Test connection by getting all users
            $users = $firebase->getAllUsers();

            $this->info('✓ Firebase connection successful!');
            $this->info("  Database contains " . count($users) . " users");

            if ($this->option('test')) {
                $this->addTestData($firebase);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Firebase connection failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->error("\nCheck your Firebase configuration in .env:");
            $this->error("  - VITE_FIREBASE_DATABASE_URL");
            $this->error("  - VITE_FIREBASE_API_KEY");

            return Command::FAILURE;
        }
    }

    private function addTestData($firebase)
    {
        $this->info("\nAdding test data...");

        // Check if admin user already exists
        $admin = $firebase->getUserByUsername('admin');
        if ($admin) {
            $this->warn("Admin user already exists, skipping...");
            return;
        }

        try {
            $firebase->createUser([
                'fullName' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);

            $this->info('✓ Test admin user created');
            $this->info("  Username: admin");
            $this->info("  Password: password123");
        } catch (\Exception $e) {
            $this->error('Failed to add test data: ' . $e->getMessage());
        }
    }
}
