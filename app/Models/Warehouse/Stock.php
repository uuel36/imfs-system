<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    public function item() {

        return $this->belongsTo(Item::class, 'item_id', 'id');

    }

    public static function isQuantityZero($id)
    {
        $stock = self::find($id);
        return $stock && $stock->quantity == 0;
    }
}
