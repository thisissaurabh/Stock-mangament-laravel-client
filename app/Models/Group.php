<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'groups';
    protected $primaryKey = 'id';

    public function brands()
    {
        return $this->belongsToMany(GroupBrand::class, 'groups_brand', 'group_id', 'id');
    }
}
