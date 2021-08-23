<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CreateItemsInventoriesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_inventories_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_inventory_id')->nullable()->unsigned();
            $table->bigInteger('item_id')->nullable()->unsigned();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('item_inventory_id')->references('id')->on('items_inventories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_inventories_items');
    }
}
