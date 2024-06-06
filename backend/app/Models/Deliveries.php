<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliveries extends Model
{
    use HasFactory;
    protected $table = 'deliveries';
    protected $fillable = [
        'order_id',
        'customer_id',
        'city',
        'state',
        'country',
        'cost',
        'status',
        'dispatch_date',
        'estimated_delivery_date',
        'notes'
    ];
}
