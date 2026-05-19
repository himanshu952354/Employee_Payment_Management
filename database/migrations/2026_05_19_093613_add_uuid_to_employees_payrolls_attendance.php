<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('uuid')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('employees',   fn (Blueprint $t) => $t->dropColumn('uuid'));
        Schema::table('payrolls',    fn (Blueprint $t) => $t->dropColumn('uuid'));
        Schema::table('attendances', fn (Blueprint $t) => $t->dropColumn('uuid'));
    }
};
