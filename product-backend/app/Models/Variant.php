<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'product_id', 'price'];

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
