<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class SubItemInventoryItem extends Model{

        use HasFactory;

        protected $table = 'sub_items_inventories_items';

        protected $fillable = ['sub_item_inventory_id', 'sub_item_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
