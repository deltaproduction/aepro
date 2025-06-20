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
    if (!Schema::hasColumn('contest_members', 'expert_id')) {
        Schema::table('contest_members', function (Blueprint $table) {
            $table->unsignedTinyInteger('expert_id')->nullable();
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contest_members', function (Blueprint $table) {
            $table->dropForeign(['expert_id']);
            $table->dropColumn('expert_id');
        });
    }
};
1