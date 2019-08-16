<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('id_card', 13);
            $table->unique(['organization_id', 'id_card']);
            $table->string('email')->nullable();
            $table->unique(['organization_id', 'email']);
            $table->string('phone', 13)->nullable();
            $table->unique(['organization_id', 'phone']);
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
        Schema::dropIfExists('person_clients');
    }
}
