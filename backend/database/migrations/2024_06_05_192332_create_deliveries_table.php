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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('customer_id');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->float('cost');
            $table->string('status', 10);
            $table->dateTime('dispatch_date');
            $table->date('estimated_delivery_date');
            $table->longText('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
