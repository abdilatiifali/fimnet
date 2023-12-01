<?php

use App\Models\Customer;
use App\Models\House;
use App\Models\Month;
use App\Models\Router;
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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignIdFor(Customer::class);
            $table->foreignIdFor(Month::class);
            $table->double('amount_paid');
            $table->string('account_number');
            $table->double('balance');
            $table->double('excess_amount')->nullable();
            $table->datetime('transaction_time');
            $table->foreignIdFor(House::class);
            $table->foreignIdFor(Router::class)->nullable();
            $table->string('paid_by');
            $table->string('phone_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
};
