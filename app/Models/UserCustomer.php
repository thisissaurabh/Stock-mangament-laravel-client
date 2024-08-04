<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCustomer extends Model
{
    use HasFactory;
    protected $table = 'user_customers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'customer_type',
        'user_customer_type',
        'first_name',
        'second_name',
        'company_name',
        'mail',
        'phone',
        'work',
        'other_details',
        'gst_no',
        'company_address',
        'city',
        'state',
        'status',
    ];
}
