<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalize emails and company names in users table
        DB::table('users')->update([
            'email' => DB::raw('LOWER(TRIM(email))'),
            'company_name' => DB::raw('TRIM(company_name)'),
        ]);

        // 2. Normalize emails and company names in employees table
        DB::table('employees')->update([
            'email' => DB::raw('LOWER(TRIM(email))'),
            'company_name' => DB::raw('TRIM(company_name)'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data transformations are typically lossy (lowercase/trimming is non-reversible)
    }
};
