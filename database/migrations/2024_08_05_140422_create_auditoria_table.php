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
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->unsignedBigInteger('rows');
            $table->unsignedBigInteger('columns');
            $table->unsignedBigInteger('contest_id');
            $table->unsignedBigInteger('place_id');
            $table->unsignedBigInteger('level_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
