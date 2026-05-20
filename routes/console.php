<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:purge {email}', function ($email) {
    $this->info("Starting purge process for email: {$email}...");

    // Delete from local SQLite
    $localUser = User::where('email', $email)->first();
    if ($localUser) {
        $localUser->delete();
        $this->info("Successfully deleted user from local SQLite database.");
    } else {
        $this->warn("User not found in local SQLite database.");
    }

    $this->info("Purge process completed successfully!");
})->purpose('Purge/delete a user and their credentials from the SQLite database');
