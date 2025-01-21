<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Currency",
 *     type="object",
 *     required={"id", "name", "symbol", "exchange_rate"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="USD"),
 *     @OA\Property(property="symbol", type="string", example="$"),
 *     @OA\Property(property="exchange_rate", type="number", format="float", example=1.00),
 * )
 */
class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }
}
