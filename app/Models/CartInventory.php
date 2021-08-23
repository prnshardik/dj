<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class CartInventory extends Model{

        use HasFactory;

        protected $table = 'cart_inventories';

        protected $fillable = ['cart_id', 'inventory_id',  'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
