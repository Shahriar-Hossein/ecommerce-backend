<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Order;
use \Illuminate\Http\JsonResponse;

class OrderController extends BaseController
{
    /**
     * index
     * @OA\Get (
     *   path="/api/v1/orders",
     *   tags={"Order"},
     *   @OA\RequestBody(
     *     required= false
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Orders retrieved successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function index(): JsonResponse
    {
        $orders = Order::query()->latest()->get();

        return $this->sendResponse($orders, 'Orders retrieved successfully.', Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = Order::query()->create($request->all());

            return $this->sendResponse($order, 'Order created successfully.', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->sendError('Error creating order.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show
     * @OA\Get (
     *   path="/api/v1/orders/{order}",
     *   tags={"Order"},
     *   @OA\Parameter(
     *      name="order",
     *      description="Order ID",
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
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Order retrieved successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Order not found."),
     *       @OA\Property(property="data", type="object", example={} ),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $order = Order::query()->findOrFail($id);

            return $this->sendResponse($order, 'Order retrieved successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Order not found.', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id): JsonResponse
    {
        try {
            $order = Order::query()->findOrFail($id);

            $order->update($request->all());

            return $this->sendResponse($order, 'Order updated successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Order not found.', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::query()->findOrFail($id);

        $order->delete();

        return $this->sendResponse([], 'Order deleted successfully.', Response::HTTP_OK);
    }
}
