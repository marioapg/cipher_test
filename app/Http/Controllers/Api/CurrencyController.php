<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/currencies",
     *     operationId="getCurrencies",
     *     tags={"Currencies"},
     *     summary="Obtener todas las divisas",
     *     description="Este endpoint devuelve una lista de todas las divisas disponibles en el sistema.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de divisas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="US Dollar"),
     *                 @OA\Property(property="symbol", type="string", example="$"),
     *                 @OA\Property(property="exchange_rate", type="number", format="float", example=1.0)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron divisas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontraron divisas")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::all();

        if ($currencies->isEmpty()) {
            return response()->json(['message' => 'No se encontraron divisas'], 404);
        }

        return response()->json($currencies, 200);
    }
}
