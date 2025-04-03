<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *    title="Parameter E-commerece API",
 *    description="Documentation for apis that can be used and their responses",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *   securityScheme="sanctum",
 *   type="http",
 *   scheme="bearer"
 * )
*/

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param $result
     * @param $message
     * @param $status
     * @return JsonResponse
     */
    public function sendResponse($result, $message, $status): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if(!empty($result)){
            $response['data'] = $result;
        }

        return response()->json($response, $status);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param $errorMessages
     * @param $status
     * @return JsonResponse
     */
    public function sendError($error,  $errorMessages , $status ): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $status);
    }
}
