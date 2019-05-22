<?php

use App\Model\Invoice\Bill;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->enum('bill_type', [Bill::TYPE_CREDIT, Bill::TYPE_CASH, Bill::TYPE_QUOTATION]);
            $table->enum('status', [Bill::STATUS_PAID, Bill::STATUS_CURRENT, Bill::STATUS_CANCEL, Bill::STATUS_EXPIRED]);
            $table->integer('total');
            $table->integer('discount')->default(0);
            $table->dateTime('expired_at')->nullable();
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
        Schema::dropIfExists('invoice_bills');
    }
}
