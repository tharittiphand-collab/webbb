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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('movie_id')->constrained()->onDelete('cascade');
        $table->foreignId('showtime_id')->nullable()->constrained()->onDelete('cascade');
        $table->integer('seat_number')->nullable();
        $table->string('status')->default('Pending');
        $table->decimal('amount', 8, 2)->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
