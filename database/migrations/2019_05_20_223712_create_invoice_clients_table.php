<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('id_card', 13)->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone', 13)->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_clients');
    }
}
