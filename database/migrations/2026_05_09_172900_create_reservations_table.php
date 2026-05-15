<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('space_id')->constrained('spaces')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])
                  ->default('pending');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone')->nullable();
            $table->string('organization')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['space_id', 'start_time', 'end_time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};