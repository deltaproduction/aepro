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
        Schema::table('contest_options', function (Blueprint $table) {
            $table->unsignedBigInteger('contest_id');
            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('variant_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contest_options', function (Blueprint $table) {
            $table->dropColumn('contest_id');
            $table->dropColumn('level_id');
            $table->dropColumn('variant_number');
        });
    }
};
