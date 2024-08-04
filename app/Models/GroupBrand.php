<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupBrand extends Model
{
    use HasFactory;
    protected $table = 'groups_brand';
    protected $primaryKey = 'id';
    protected $fillable = ['group_id', 'brand_name'];
    // public function group()
    // {
    //     return $this->belongsTo(Group::class, 'group_id');
    // }
}
