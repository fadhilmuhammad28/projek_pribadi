<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->string('plate_number');
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->foreignId('parking_lot_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['entry', 'exited', 'paid'])->default('entry');
            $table->decimal('fee', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

