<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Item extends Model{

        use HasFactory;

        protected $table = 'items';

        protected $fillable = ['category_id', 'name', 'description', 'image', 'qrcode', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
