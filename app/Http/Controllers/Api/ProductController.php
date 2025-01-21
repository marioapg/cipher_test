<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Obtener la lista de productos",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
     *
     * Obtener lista de productos
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json($products, 200);
    }
}
