<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Cart extends Model{

        use HasFactory;

        protected $table = 'cart';

        protected $fillable = ['redispatch_id' ,'user_id', 'party_name', 'party_address', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
