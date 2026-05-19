<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Services\MongoDBService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:purge {email}', function ($email) {
    $this->info("Starting purge process for email: {$email}...");

    // 1. Delete from local SQLite
    $localUser = User::where('email', $email)->first();
    if ($localUser) {
        $localUser->delete();
        $this->info("Successfully deleted user from local SQLite database.");
    } else {
        $this->warn("User not found in local SQLite database.");
    }

    // 2. Delete from MongoDB Atlas
    try {
        $mongoService = new MongoDBService();
        $mongoUsers = $mongoService->selectCollection('users');
        $result = $mongoUsers->deleteOne(['email' => $email]);
        
        if (($result['deletedCount'] ?? 0) > 0) {
            $this->info("Successfully deleted user from MongoDB Atlas cloud database.");
        } else {
            $this->warn("User not found in MongoDB Atlas cloud database users collection.");
        }
    } catch (\Exception $e) {
        $this->error("Failed to delete user from MongoDB Atlas: " . $e->getMessage());
    }

    $this->info("Purge process completed successfully!");
})->purpose('Purge/delete a user and their credentials from both SQLite and MongoDB Atlas');
