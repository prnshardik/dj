<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class CartUser extends Model{

        use HasFactory;

        protected $table = 'cart_users';

        protected $fillable = ['cart_id', 'user_id',  'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
