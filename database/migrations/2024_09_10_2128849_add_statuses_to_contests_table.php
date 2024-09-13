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
        Schema::table('contests', function (Blueprint $table) {
            $table->unsignedTinyInteger('options_status')->nullable();
            $table->unsignedTinyInteger('auditoriums_status')->nullable();
            $table->unsignedTinyInteger('protocols_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn('options_status');
            $table->dropColumn('auditoriums_status');
            $table->dropColumn('protocols_status');
        });
    }
};
