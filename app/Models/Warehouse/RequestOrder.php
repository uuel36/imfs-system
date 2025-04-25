<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestOrder extends Model
{
    use HasFactory;
    protected $table = 'requestorders'; 

    protected $fillable = [
        'stock_id',
        'item_id',
        'area_id',
        'quantity',
        'leadman_id',
        'remaining_quantity',
        'is_returned',
        'is_approved',
        'is_quantity',
        'is_disapproved',
        'date_returned',
        'date',
        'date_time_received',
    ];
    public function deploy()
    {
        return $this->hasOne(Deploy::class);
    }
}
