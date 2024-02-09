<?php

namespace App\Models;

use App\Http\Requests\ProductInsertRequest;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewestVariant extends Model
{
    public static function withNewestVariant(BaseSearchObject $searchObject)
    {
        return Product::select('products.id', 'products.name', 'v.price', 'v.id as variantId', 'v.name as variantName')
            ->joinSub(
                Variant::selectRaw('DISTINCT ON (product_id) product_id, id, name, price')
                    ->orderBy('product_id')
                    ->orderByDesc('created_at'),
                'v', function ($join) {
                $join->on('products.id', '=', 'v.product_id');
            }
            )->orderBy('product_id')->paginate($searchObject->size);

//        $query = Variant::select('price')->where('price', '>', 100)->toSql();
//        $queryBindings = Variant::where('price', '>', 100)->getBindings();
//        $executionPlan = DB::select('EXPLAIN ' . $query, $queryBindings);
//        dump($executionPlan);
    }

    publIc static function firstFiveProductsPrices()
    {
        $results = DB::table('variants')
            ->select('product_id', DB::raw('SUM(price) AS TotalPrice'))
            ->where('product_id', '<=', 5)
            ->groupBy('product_id')
            ->orderBy('product_id', 'asc')
            ->get();

        return $results;
    }
}
