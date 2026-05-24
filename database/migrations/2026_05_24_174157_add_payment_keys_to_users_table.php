<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_key')->nullable()->after('currency');
            $table->string('stripe_secret')->nullable()->after('stripe_key');
            $table->string('paypal_client_id')->nullable()->after('stripe_secret');
            $table->string('paypal_client_secret')->nullable()->after('paypal_client_id');
            $table->string('paypal_mode')->default('sandbox')->after('paypal_client_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_key',
                'stripe_secret',
                'paypal_client_id',
                'paypal_client_secret',
                'paypal_mode'
            ]);
        });
    }
};
