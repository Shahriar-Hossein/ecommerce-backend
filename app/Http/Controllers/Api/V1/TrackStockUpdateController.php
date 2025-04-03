<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\TrackStockUpdate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TrackStockUpdateController extends BaseController
{
    /**
     * index
     * @OA\Get (
     *   path="/api/v1/products/{product}/track-stock-updates",
     *   tags={"Product"},
     *   @OA\RequestBody(
     *     required= false
     *   ),
     *   @OA\Parameter(
     *     name="product",
     *     description="Product ID",
     *     in="path",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Product stock updates retrieved successfully"),
     *       @OA\Property(property="data", type="array", collectionFormat="multi",
     *         @OA\Items(type="object"),
     *         example={}
     *       ),
     *     )
     *   ),
     *   security={{"sanctum": {}}}
     * )
     */
    public function index(Request $request,$product): JsonResponse
    {
        try {
            Product::query()->findOrFail($product);

            $tracks = TrackStockUpdate::query()->where('product_id',$product)->latest()->get();

            return $this->sendResponse($tracks, 'Stock changes retrieved successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error) {
            return $this->sendError('Product not found.', [], ResponseAlias::HTTP_NOT_FOUND);

        } catch (Throwable $error) {
            return $this->sendError('Internal server error.', [], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
