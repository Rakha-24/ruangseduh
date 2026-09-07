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
            $table->foreignId('dining_table_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->date('reservation_date')->index();
            $table->time('reservation_time')->index();
            $table->unsignedInteger('guest_count');
            $table->string('status', 20)->default('pending')->index();
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};