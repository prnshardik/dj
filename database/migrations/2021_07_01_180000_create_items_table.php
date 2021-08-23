<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->nullable()->unsigned();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->text('qrcode')->nullable();
            $table->enum('status', ['active', 'inactive', 'deleted', 'repairing'])->default('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('category_id')->references('id')->on('items_categories')->onDelete('cascade')->onUpdate('cascade');
        });

        $file_to_upload = public_path().'/uploads/items/';
        if (!File::exists($file_to_upload))
            File::makeDirectory($file_to_upload, 0777, true, true);

        $qr_to_upload = public_path().'/uploads/qrcodes/items/';
            if (!File::exists($qr_to_upload))
                File::makeDirectory($qr_to_upload, 0777, true, true);

        if(file_exists(public_path('/qr_logo.png')) && !file_exists(public_path('/uploads/items/default.png')) ){
            File::copy(public_path('/qr_logo.png'), public_path('/uploads/items/default.png'));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
