<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table = 'log';

    protected $fillable = ['item_id','item_type','type','status','created_at','created_by','updated_at','updated_by'];
}
