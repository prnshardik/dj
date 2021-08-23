<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class ItemInventory extends Model{

        use HasFactory;

        protected $table = 'items_inventories';

        protected $fillable = ['title', 'description', 'image', 'qrcode', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
