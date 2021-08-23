<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CreateSubItemsInventoriesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_items_inventories_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sub_item_inventory_id')->nullable()->unsigned();
            $table->bigInteger('sub_item_id')->nullable()->unsigned();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('sub_item_inventory_id')->references('id')->on('sub_items_inventories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sub_item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_items_inventories_items');
    }
}
