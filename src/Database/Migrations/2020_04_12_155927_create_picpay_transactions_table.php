<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicpayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picpay_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('reference_id');
            $table->string('payment_url')->nullable();
            $table->text('qr_code')->nullable();
            $table->dateTimeTz('expires_at')->nullable();
            $table->string('status')->nullable()->default('created');
            $table->timestamps();

            $table->foreign('reference_id')->references('id')->on('cart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picpay_transactions', function (Blueprint $table) {
            $table->dropForeign('picpay_transactions_reference_id_foreign');
        });

        Schema::dropIfExists('picpay_transactions');
    }
}