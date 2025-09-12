<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type')->nullable(); // system, admin, teacher, student
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action'); // attendance.recorded, attendance.rejected, student.created, etc.
            $table->string('nfc_id')->nullable();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
            
            $table->index(['action', 'created_at']);
            $table->index(['nfc_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};


