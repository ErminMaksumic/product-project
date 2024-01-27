<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['product_type_id', 'name', 'description', 'price'];

    public function productType() : BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function variants() : HasMany
    {
        return $this->hasMany(Variant::class);
    }
}
