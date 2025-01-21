<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Modelo de Producto",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Producto 1"),
 *     @OA\Property(property="description", type="string", example="Descripción del producto"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="currency_id", type="integer", example=1),
 *     @OA\Property(property="tax_cost", type="number", format="float", example=15.00),
 *     @OA\Property(property="manufacturing_cost", type="number", format="float", example=30.00)
 * )
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost'
    ];

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
