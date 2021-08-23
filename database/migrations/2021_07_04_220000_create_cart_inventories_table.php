<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_inventories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cart_id')->nullable()->unsigned();
            $table->bigInteger('inventory_id')->nullable()->unsigned();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('cart_id')->references('id')->on('cart')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('inventory_id')->references('id')->on('items_inventories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_inventories');
    }
}
