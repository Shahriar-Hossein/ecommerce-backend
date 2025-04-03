<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CategoryController extends BaseController
{
    /**
     * index
     * @OA\Get (
     *   path="/api/v1/categories",
     *   tags={"Category"},
     *   @OA\RequestBody(
     *     required= false
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     * )
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()->latest()->get();

        return $this->sendResponse($categories, 'Categories retrieved successfully.', ResponseAlias::HTTP_OK);
    }

    /**
     * Store
     * @OA\Post (
     *   path="/api/v1/categories",
     *   tags={"Category"},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={"title","image"},
     *         @OA\Property(property="title", type="string", example=""),
     *         @OA\Property(property="image", type="file"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Category created successfully"),
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
     *           property="title", type="array", collectionFormat="multi",
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

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::query()->create($request->all());

        $category->uploadImage($request);

        return $this->sendResponse($category, 'Category created successfully.', ResponseAlias::HTTP_CREATED);
    }

    /**
     * Show
     * @OA\Get (
     *   path="/api/v1/categories/{category}",
     *   tags={"Category"},
     *   @OA\Parameter(
     *      name="category",
     *      description="Category ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     required= false
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Category retrieved successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Category not found."),
     *       @OA\Property(property="data", type="object", example={} ),
     *     )
     *   ),
     * )
     */
    public function show($category): JsonResponse
    {
        try{
            $category = Category::query()->findOrFail($category);

            $category->products = Product::query()->where('category_id', $category->id)->get();

            return $this->sendResponse($category, 'Category retrieved successfully.', ResponseAlias::HTTP_CREATED);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Category not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update
     * @OA\Post (
     *   path="/api/v1/categories/{category}",
     *   tags={"Category"},
     *   @OA\Parameter(
     *      name="category",
     *      description="Category ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={"_method"},
     *         @OA\Property(property="_method",type="string", example="PUT"),
     *         @OA\Property(property="title", type="string", description="Title of the category", example="",),
     *         @OA\Property(property="image", type="file", description="Image for the category",),
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Category updated successfully"),
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
     *           property="title", type="array", collectionFormat="multi",
     *           @OA\Items(type="string", example="")
     *         ),
     *       ),
     *     ),
     *   ),@OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Category not found"),
     *       @OA\Property(property="data", type="object", example={} ),
     *     ),
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function update(UpdateCategoryRequest $request, $category): JsonResponse
    {
        try{
            $category = Category::query()->findOrFail($category);
            $category->update($request->all());

            $category->uploadImage($request);

            return $this->sendResponse($category, 'Category updated successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Category not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Destroy
     * @OA\Delete (
     *   path="/api/v1/categories/{category}",
     *   tags={"Category"},
     *   @OA\Parameter(
     *      name="category",
     *      description="Category ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     required= false
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Category deleted successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Category not found."),
     *       @OA\Property(property="data", type="object", example={} ),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function destroy($category): JsonResponse
    {
        try{
            $category = Category::query()->findOrFail($category);

            $category->delete();

            return $this->sendResponse([], 'Category deleted successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Category not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }
}
