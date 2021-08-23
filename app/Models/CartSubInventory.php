<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class CartSubInventory extends Model{

        use HasFactory;

        protected $table = 'cart_sub_inventories';

        protected $fillable = ['cart_id', 'sub_inventory_id',  'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
