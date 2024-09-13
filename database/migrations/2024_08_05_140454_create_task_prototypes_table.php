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
        Schema::create('task_prototypes', function (Blueprint $table) {
            $table->id();
            $table->text("task_text")->nullable();
            $table->text("task_answer")->nullable();
            $table->unsignedBigInteger("prototype_number");
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('contest_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_prototypes');
    }
};
