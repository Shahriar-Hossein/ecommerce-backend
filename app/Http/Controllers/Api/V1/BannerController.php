<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BannerController extends BaseController
{

    /**
     * Index
     * @OA\Get (
     *   path="/api/v1/banners",
     *   tags={"Banner"},
     *   @OA\RequestBody(
     *     required=false,
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Banners retrieved successfully."),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         example={
     *           {
     *             "id": 3,
     *             "title": "Flash Sale",
     *             "subtitle": "30% discount on all t-shirts",
     *             "image": "SAO2",
     *             "button_url": "www.localhost:8000",
     *             "button_text": "Buy Now",
     *             "image_url": "http://localhost:8000/storage/8/SAO2.jpg"
    *            },
    *            {
     *             "id": 4,
     *             "title": "Flash Sale",
     *             "subtitle": "30% discount on all t-shirts",
     *             "image": "SAO2",
     *             "button_url": "www.localhost:8000",
     *             "button_text": "Buy Now",
     *             "image_url": "http://localhost:8000/storage/44/SAO2.jpg"
     *            },
     *         }
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Unauthorized."),
     *     )
     *   ),
     * )
     **/
    public function index(): JsonResponse
    {
        $banners = Banner::query()->latest()->get();

        return $this->sendResponse($banners, 'Banners retrieved successfully.', ResponseAlias::HTTP_OK);
    }

    /**
     * Store
     * @OA\Post (
     *   path="/api/v1/banners",
     *   tags={"Banner"},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={"title", "subtitle", "description", "button_url", "button_text"},
     *         @OA\Property(property="title", type="string", description="Write the title of the banner", example=""),
     *         @OA\Property(property="subtitle", type="string", description="Write the subtitle of the banner", example=""),
     *         @OA\Property(property="description", type="longtext", description="Write the description of the banner", example=""),
     *         @OA\Property(property="button_url", type="string", description="Write the full url", example=""),
     *         @OA\Property(property="button_text", type="string", description="Write the button text", example=""),
     *         @OA\Property(property="image", type="file", description="Upload banner image")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Banner created successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(property="data", type="object", example={} ),
     *     ),
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function store(StoreBannerRequest $request): JsonResponse
    {
        $banner = Banner::query()->create($request->all());

        $banner->uploadImage($request);

        return $this->sendResponse($banner, 'Banner created successfully.', ResponseAlias::HTTP_CREATED);
    }

    /**
     * Show
     * @OA\Get (
     *   path="/api/v1/banners/{banner}",
     *   tags={"Banner"},
     *   @OA\RequestBody(required= false),
     *   @OA\Parameter(
     *     name="banner",
     *     description="Banner ID",
     *     required=true,
     *     in="path",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Banner retrieved successfully."),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not Found",
     *      @OA\JsonContent(
     *        @OA\Property(property="success", type="boolean", example=false),
     *        @OA\Property(property="message", type="string", example="Banner not found."),
     *      )
     *    ),
 * )
     */
    public function show(string $id): JsonResponse
    {
        try{
            $banner = Banner::query()->findOrFail($id);

            return $this->sendResponse($banner, 'Banner retrieved successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){

            return $this->sendError('Banner not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update
     * @OA\Post (
     *   path="/api/v1/banners/{banner}",
     *   tags={"Banner"},
     *   @OA\Parameter(
     *      name="banner",
     *      description="Banner ID",
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
     *         @OA\Property(property="title", type="string", description="Write the title of the banner", example=""),
     *         @OA\Property(property="subtitle", type="string", description="Write the subtitle of the banner", example=""),
     *         @OA\Property(property="description", type="longtext", description="Write the description of the banner", example=""),
     *         @OA\Property(property="button_url", type="string", description="Write the price of the produce", example=""),
     *         @OA\Property(property="button_text", type="string", description="Write the currency name", example=""),
     *         @OA\Property(property="image", type="file", description="Upload banner image")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Banner updated successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(property="data", type="object", example={} ),
     *     ),
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function update(UpdateBannerRequest $request, string $id): JsonResponse
    {
        try{

            $banner = Banner::query()->findOrFail($id);
            $banner->update($request->all());

            if($request->hasFile('image')){
                $banner->uploadImage($request);
            }

            return $this->sendResponse($banner, 'Banner updated successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Banner not found.', [], ResponseAlias::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete
     * @OA\Delete (
     *   path="/api/v1/banners/{banner}",
     *   tags={"Banner"},
     *   @OA\Parameter(
     *      name="banner",
     *      description="Banner ID",
     *      example=1,
     *      required=true,
     *      in="path",
     *   ),
     *   @OA\RequestBody(
     *     required=false
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Banner deleted successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Banner not found."),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try{
            $banner = Banner::query()->findOrFail($id);

            $banner->delete();

            return $this->sendResponse([], 'Banner deleted successfully.', ResponseAlias::HTTP_OK);

        } catch(ModelNotFoundException $error){
            return $this->sendError('Banner not found.',[], ResponseAlias::HTTP_NOT_FOUND);
        }
    }
}
