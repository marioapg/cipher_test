<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        return response()->json($product->load('prices'), 201);
    }

    /**
     * @OA\Schema(
     *     schema="Product",
     *     type="object",
     *     required={"id", "name", "description", "price", "currency_id"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Producto A"),
     *     @OA\Property(property="description", type="string", example="Descripción del Producto A"),
     *     @OA\Property(property="price", type="number", format="float", example=100.00),
     *     @OA\Property(property="currency_id", type="integer", example=1),
     *     @OA\Property(property="tax_cost", type="number", format="float", example=20.00),
     *     @OA\Property(property="manufacturing_cost", type="number", format="float", example=50.00),
     * )
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     summary="Actualizar un producto y sus precios",
     *     description="Este endpoint permite actualizar los detalles de un producto, incluyendo los precios en diferentes divisas. Los precios existentes se sincronizan, permitiendo la eliminación de precios de divisas no incluidas.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
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
     *         response=200,
     *         description="Producto actualizado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida"
     *     )
     * )
     */
    public function update(Request $request, Product $product): JsonResponse
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

            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'price' => $validated['price'],
                'currency_id' => $validated['currency_id'],
                'tax_cost' => $validated['tax_cost'] ?? 0,
                'manufacturing_cost' => $validated['manufacturing_cost'] ?? 0,
            ]);

            $pricesData = collect($validated['prices'])->mapWithKeys(function ($priceData) {
                return [$priceData['currency_id'] => ['price' => $priceData['price']]];
            });
            
            foreach ($pricesData as $currencyId => $priceData) {
                ProductPrice::updateOrCreate(
                    ['product_id' => $product->id, 'currency_id' => $currencyId],
                    $priceData
                );
            }

            $existingCurrencyIds = collect($validated['prices'])->pluck('currency_id');
            ProductPrice::where('product_id', $product->id)
                        ->whereNotIn('currency_id', $existingCurrencyIds)
                        ->delete();

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        return response()->json($product->load('prices'), 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     summary="Eliminar un producto",
     *     description="Este endpoint permite eliminar un producto y sus precios asociados.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida"
     *     )
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->prices()->delete();
            $product->delete();
            
            return response()->json(['message' => 'Producto eliminado exitosamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}/prices",
     *     operationId="getProductPrices",
     *     tags={"Products"},
     *     summary="Obtener lista de precios de un producto",
     *     description="Este endpoint devuelve la lista de precios asociados a un producto en diferentes divisas.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de precios obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductPrice")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto no encontrado")
     *         )
     *     )
     * )
     */
    public function getPrices(Product $product): JsonResponse
    {
        try {
            $prices = $product->prices;

            return response()->json($prices, 200);
        } catch (ModelNotFoundException $th) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products/{id}/prices",
     *     operationId="createProductPrice",
     *     tags={"Products"},
     *     summary="Crear un nuevo precio para un producto",
     *     description="Este endpoint permite agregar un nuevo precio para un producto en una divisa específica.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"currency_id", "price"},
     *             @OA\Property(property="currency_id", type="integer", example=2),
     *             @OA\Property(property="price", type="number", format="float", example=120.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Precio creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/ProductPrice")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida"
     *     )
     * )
     */
    public function createPrice(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'price' => 'required|numeric|min:0',
        ]);

        $existingPrice = ProductPrice::where('product_id', $product->id)
                                    ->where('currency_id', $validated['currency_id'])
                                    ->first();

        if ($existingPrice) {
            return response()->json(['message' => 'El producto ya tiene un precio en esta divisa.'], 400);
        }

        $price = ProductPrice::create([
            'product_id' => $product->id,
            'currency_id' => $validated['currency_id'],
            'price' => $validated['price'],
        ]);

        return response()->json($price, 201);
    }

}
