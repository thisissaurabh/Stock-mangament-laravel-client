<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discounts';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'type', 'value', 'applies_to', 'user_id', 'starts_at', 'ends_at'];
}
