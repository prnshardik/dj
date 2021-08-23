<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class SubItem extends Model{

        use HasFactory;

        protected $table = 'sub_items';

        protected $fillable = ['sub_item_category_id', 'name', 'description', 'image', 'qrcode', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
