<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_clients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('tour_id');
            $table->string('client_name');
            $table->string('unique_id');
            $table->boolean('is_booked');
            $table->boolean('is_attended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_clients');
    }
};
