<?php

namespace App\Http\Requests;

use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class BaseFormRequest extends FormRequest
{
    /**
     * @throws BindingResolutionException
     */
    protected function failedValidation(Validator $validator)
    {
        $controller = app()->make(BaseController::class);
        throw new HttpResponseException(
            $controller->sendError('Validation error.', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
