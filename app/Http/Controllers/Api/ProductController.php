<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
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

    /**
     * @OA\Post(
     *     path="/api/products",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     summary="Crear un nuevo producto",
     *     description="Crea un producto con su precio en la divisa base y otros precios en diferentes divisas.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "currency_id", "prices"},
     *             @OA\Property(property="name", type="string", example="Producto A"),
     *             @OA\Property(property="description", type="string", example="Descripción del Producto A"),
     *             @OA\Property(property="price", type="number", format="float", example=100.00),
     *             @OA\Property(property="currency_id", type="integer", example=1),
     *             @OA\Property(property="tax_cost", type="number", format="float", example=20.00),
     *             @OA\Property(property="manufacturing_cost", type="number", format="float", example=50.00),
     *             @OA\Property(
     *                 property="prices", type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="currency_id", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", format="float", example=120.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida",
     *     ),
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'currency_id' => 'required|exists:currencies,id',
                'tax_cost' => 'nullable|numeric|min:0',
                'manufacturing_cost' => 'nullable|numeric|min:0',
                'prices' => 'required|array',
                'prices.*.currency_id' => 'required|exists:currencies,id',
                'prices.*.price' => 'required|numeric|min:0',
            ]);
            
        
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'price' => $validated['price'],
                'currency_id' => $validated['currency_id'],
                'tax_cost' => $validated['tax_cost'] ?? 0,
                'manufacturing_cost' => $validated['manufacturing_cost'] ?? 0,
            ]);

            foreach ($validated['prices'] as $priceData) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'currency_id' => $priceData['currency_id'],
                    'price' => $priceData['price'],
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        return response()->json($product, 201);
    }
}
