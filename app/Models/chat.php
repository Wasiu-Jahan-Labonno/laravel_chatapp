<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    protected $fillable = ['user_id','other_user_id','group_id','read_id'];
    // use HasFactory;
}
