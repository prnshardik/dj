<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CreateSubItemsInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_items_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->text('qrcode')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        $file_to_upload = public_path().'/uploads/sub_items_inventory/';
        if (!File::exists($file_to_upload))
            File::makeDirectory($file_to_upload, 0777, true, true);

        $qr_to_upload = public_path().'/uploads/qrcodes/sub_items_inventory/';
            if (!File::exists($qr_to_upload))
                File::makeDirectory($qr_to_upload, 0777, true, true);

        if(file_exists(public_path('/qr_logo.png')) && !file_exists(public_path('/uploads/sub_items_inventory/default.png')) ){
            File::copy(public_path('/qr_logo.png'), public_path('/uploads/sub_items_inventory/default.png'));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_items_inventories');
    }
}
