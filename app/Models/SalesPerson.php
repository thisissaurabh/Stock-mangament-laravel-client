<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    use HasFactory;
    protected $table = 'sales_persons';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'name', 'email', 'phone', 'photo'];
}
