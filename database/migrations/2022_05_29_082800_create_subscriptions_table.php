<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('month_id')->constrained();
            $table->foreignId('session_id')->default(1);
            $table->double('amount');
            $table->double('amount_paid')->default(0.0);
            $table->double('balance');
            $table->boolean('paid')->default(false);
            $table->timestamps();

            $table->unique(['customer_id', 'month_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
