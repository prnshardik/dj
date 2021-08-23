<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartSubInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_sub_inventories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cart_id')->nullable()->unsigned();
            $table->bigInteger('sub_inventory_id')->nullable()->unsigned();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('cart_id')->references('id')->on('cart')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sub_inventory_id')->references('id')->on('sub_items_inventories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_sub_inventories');
    }
}
