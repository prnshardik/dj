<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class ItemInventoryItem extends Model{

        use HasFactory;

        protected $table = 'items_inventories_items';

        protected $fillable = ['item_inventory_id', 'item_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
