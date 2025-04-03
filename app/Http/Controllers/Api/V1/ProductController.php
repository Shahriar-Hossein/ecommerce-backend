<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Mail\NotifyUser;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    /**
     * index
     * @OA\Get (
     *   path="/api/v1/products",
     *   tags={"Product"},
     *   @OA\RequestBody(
     *     required= false
     *   ),
     *   @OA\Parameter(
     *     name="filter",
     *     description="Filter with trending or new-arrival",
     *     in="query",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *       @OA\Property(property="data", type="array", @OA\Items(type="object"), example={}),
     *     )
     *   ),
     * )
     */
    public function index(Request $request, $category_id = null): JsonResponse
    {
        try{
            $filter = $request->query('filter');

            $products = Product::query()
                ->when($filter === 'new-arrival', fn($query) => $query->where('created_at', '>=', Carbon::now()->subDays(30)))
                ->when($filter === 'trending', fn($query) => $query->where('trending', 1))
                ->when($category_id, fn($query) => $query->where('category_id', $category_id))
                ->latest()
                ->get();

            return $this->sendResponse($products, 'Products retrieved successfully.', ResponseAlias::HTTP_OK);

        } catch (Throwable $error) {
            return $this->sendError('Internal server error.', [], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search
     * @OA\Get (
     *   path="/api/v1/products/search",
     *   tags={"Product"},
     *   @OA\RequestBody(
     *     required=false
     *   ),
     *   @OA\Parameter(
     *     name="title",
     *     description="Search by title",
     *     in="query",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *       @OA\Property(property="data", type="array", @OA\Items(type="object"), example={}),
     *     )
     *   ),
     * )
     */
    public function search(Request $request): JsonResponse
    {
        try{
            $products = Product::query()->where('title', 'like',"%$request->title%")->get();

            return $this->sendResponse($products, 'Products retrieved successfully.', ResponseAlias::HTTP_OK);

        } catch (Throwable $error) {
            return $this->sendError('Internal server error.', $error->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store
     * @OA\Post (
     *   path="/api/v1/categories/{category_id}/products",
     *   tags={"Product"},
     *   @OA\Parameter(
     *      name="category_id",
     *      description="Category ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={"title", "stock_quantity","price", "currency", "description"},
     *         @OA\Property(property="title", type="string", description="Write the title of the product", example=""),
     *         @OA\Property(property="stock_quantity",type="integer", description="Write the quantity in stock", example=""),
     *         @OA\Property(property="price",type="integer", description="Write the price of the product", example=""),
     *         @OA\Property(property="currency",type="string", description="Write the currency name", example=""),
     *         @OA\Property(property="trending",type="boolean", description="Is the product trend   ing?", enum={1,0}),
     *         @OA\Property(property="description",type="string", description="Write the description of the product", example=""),
     *         @OA\Property(
     *           property="images[]",
     *           type="array",
     *           collectionFormat="multi",
     *           @OA\Items(type="file"),
     *         )
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Product created successfully"),
     *       @OA\Property(property="data", type="object",
     *         example={}
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(
     *         property="data", type="object",
     *         @OA\Property(
     *           property="title", type="array",
     *           @OA\Items(type="string", example="The title already exists")
     *         ),
     *       ),
     *     ),
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function store(StoreProductRequest $request, $category_id): JsonResponse
    {
        try {
            Category::query()->findOrFail($category_id);
            $product = Product::query()->create(array_merge($request->all(), ['category_id' => $category_id]));

            $request->hasFile('images') ? $product->uploadImages($request->file('images')) : null;

            // add record in track_stock_updates table
            $product->trackStockUpdate($request->note ?? null );

            return $this->sendResponse($product, 'Product created successfully.', ResponseAlias::HTTP_CREATED);

        } catch (ModelNotFoundException $error) {
                return $this->sendError('Category not found.', [], ResponseAlias::HTTP_NOT_FOUND);

        } catch (Throwable $error) {
            return $this->sendError('Internal server error.', [], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Show
     * @OA\Get (
     *   path="/api/v1/products/{product}",
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
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *       @OA\Property(property="data", type="array", @OA\Items(type="object"), example={}),
     *     )
     *   ),
     * )
     */
    public function show(string $id): JsonResponse
    {
        try{
            $product = Product::query()->findOrFail($id);

            return $this->sendResponse($product, 'Product retrieved successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Product not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update
     * @OA\Post (
     *   path="/api/v1/products/{product}",
     *   tags={"Product"},
     *   @OA\Parameter(
     *      name="product",
     *      description="Product ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={},
     *         @OA\Property(property="_method", type="string",example="PUT"),
     *         @OA\Property(property="title", type="string", description="Write the title of the product", example=""),
     *         @OA\Property(property="stock_quantity", type="integer", description="Write the quantity in stock", example=""),
     *         @OA\Property(property="price", type="integer", description="Write the price of the produce", example=""),
     *         @OA\Property(property="currency", type="string", description="Write the currency name", example=""),
     *         @OA\Property(property="trending", type="boolean", description="Is the product trending?", enum={1,0}),
     *         @OA\Property(property="description", type="string", description="Write the description of the product", example=""),
     *         @OA\Property(
     *           property="images[]",
     *           type="array",
     *           collectionFormat="multi",
     *           @OA\Items(type="file"),
     *         )
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Product updated successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(
     *         property="data", type="object",
     *         @OA\Property(
     *           property="title", type="array",
     *           @OA\Items(type="string", example="The title has already been taken.")
     *         ),
     *       ),
     *     ),
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        try{
            $product = Product::query()->findOrFail($id);
//            To Do
            // if($product->stock_quantity == 0 && $request->stock_quantity > 0 && $product->notify_users){
            //     foreach($product->notify_users as $user_id){
            //         $user = User::findOrFail($user_id);
            //         if($user){
            //             Mail::to($user->email)->send(new NotifyUser());
            //         }
            //     }
            //     $product->notify_users = [];
            // }

            // to get the previous stock_quantity and compare it with the new stock_quantity
            // had to fill the product with the request data before updating the stock_quantity
            $product->fill($request->all());

            $request->filled("stock_quantity") ? $product->trackStockUpdate($request->note ?? null ) : null ;

            $product->save();

            return $this->sendResponse($product, 'Product updated successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Product not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete
     * @OA\Delete (
     *   path="/api/v1/products/{product}",
     *   tags={"Product"},
     *   @OA\Parameter(
     *      name="product",
     *      description="Product ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     required=false,
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Product deleted successfully."),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Unauthorized."),
     *       @OA\Property(property="data", type="object", example={}),
     *     ),
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try{
            $product = Product::query()->findOrFail($id);

            $product->delete();

            return $this->sendResponse([], 'Product deleted successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Product not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Product Request
     * @OA\Put (
     *   path="/api/v1/products/{product}/request",
     *   tags={"Product"},
     *   @OA\Parameter(
     *      name="product",
     *      description="Product ID",
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(required=false),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Product requested successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function productRequest(Request $request,$product): JsonResponse
    {
        try{
            $user = $request->user();

            $product = Product::query()->findOrFail($product);

            $notifyUsers = $product['notify_users'] ?? [];

            if (in_array($user->id, $notifyUsers)) {
                return $this->sendResponse([], 'You have already requested for this product.', ResponseAlias::HTTP_OK);
            }

            $product['notify_users'] = array_merge($notifyUsers, [$user->id]);;

            $product->save();

            return $this->sendResponse([], 'You will be notified when product arrives.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Product not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }
}
