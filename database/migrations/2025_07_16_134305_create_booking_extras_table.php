<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('extra_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price_at_booking', 8, 2); // Store price at time of booking
            $table->timestamps();
            
            $table->unique(['booking_id', 'extra_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_extras');
    }
};