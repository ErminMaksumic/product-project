<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewestVariant extends Model
{
    public static function withNewestVariant()
    {
        return Product::select('products.id', 'products.name', 'v.price', 'v.id as variantId', 'v.name as variantName')
            ->joinSub(
                Variant::selectRaw('DISTINCT ON (product_id) product_id, id, name, price')
                    ->orderBy('product_id')
                    ->orderByDesc('created_at'),
                'v', function ($join) {
                $join->on('products.id', '=', 'v.product_id');
            }
            )->orderBy('product_id')
            ->get();
    }
}
