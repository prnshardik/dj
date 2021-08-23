<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->text('image')->nullable();
            $table->enum('is_admin', ['y', 'n'])->default('n');
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('inactive');
            $table->rememberToken();
            $table->string('device_id')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        $file_to_upload = public_path().'/uploads/users/';
        if (!File::exists($file_to_upload))
            File::makeDirectory($file_to_upload, 0777, true, true);

        if(file_exists(public_path('/qr_logo.png')) && !file_exists(public_path('/uploads/users/default.png')) ){
            File::copy(public_path('/qr_logo.png'), public_path('/uploads/users/default.png'));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
