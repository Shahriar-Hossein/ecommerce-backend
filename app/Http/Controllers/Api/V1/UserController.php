<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    /**
     * Register
     * @OA\Post (
     *   path="/api/v1/register",
     *   tags={"User"},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={"name","email","password","confirm_password",},
     *         @OA\Property(property="name",type="string", example=""),
     *         @OA\Property(property="email",type="string", example=""),
     *         @OA\Property(property="password",type="string", example=""),
     *         @OA\Property(property="confirm_password",type="string", example="")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User registered successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(
     *         property="data", type="object",
     *         @OA\Property(
     *           property="email", type="array", collectionFormat="multi",
     *           @OA\Items(type="string",example="The email has already been taken.")
     *         ),
     *       ),
     *     ),
     *   ),
     * )
     */
    public function register(StoreUserRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $user = User::query()->create($input);
        $data['token'] =  $user->createToken('Authentication')->plainTextToken;

        return $this->sendResponse($data, 'User registered successfully.', ResponseAlias::HTTP_CREATED);
    }

    /**
     * Login
     * @OA\Post (
     *   path="/api/v1/login",
     *   tags={"User"},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         required= {"email","password"},
     *         @OA\Property( property="email", type="string", example="shihab@gmail.com"),
     *         @OA\Property( property="password", type="string",example="12345678")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User logged in successfully"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         example={
     *           "token":"19|72aHg45WwAOXiR7pzAqjgG0k24mOWseGwv3iszRP4cc5ffa0"
     *         }
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Unauthorized."),
     *       @OA\Property(
     *         property="data", type="object",
     *         @OA\Property(property="error", type="string", example="Wrong Password."),
     *       ),
     *     ),
     *   )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email:rfc,dns|max:255',
            'password' => 'required|min:8|max:255',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::query()->where('email', $request->get('email'))->first();

        if ( empty($user) ) {
            return $this->sendError('Invalid User.', ['error'=>'The user does not exist'], ResponseAlias::HTTP_NOT_FOUND);
        }

        if (Hash::check($request->get('password'), $user->password)) {

            $data['token'] =  $user->createToken('Authentication')->plainTextToken;

            return $this->sendResponse($data, 'User logged in successfully.', ResponseAlias::HTTP_OK);

        } else {
            return $this->sendError('Unauthorized.', ['error'=>'Wrong Password'], ResponseAlias::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout
     * @OA\Post (
     *   path="/api/v1/logout",
     *   summary="Logout user",
     *   description="Logs out the authenticated user by deleting their access token",
     *   tags={"User"},
     *   @OA\RequestBody(required=false,),
     *   @OA\Response(
     *     response=200,
     *     description="User logged out successfully",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User logged out successfully.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string",example="Unauthorized."),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'User logged out successfully.', ResponseAlias::HTTP_OK);
    }

    /**
     * show
     * @OA\Get (
     *   path="/api/v1/user",
     *   tags={"User"},
     *   @OA\RequestBody(
     *     required=false,
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User detail retrieved successfully."),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         example={
     *           "id":1,
     *           "name": "Shihab",
     *           "email": "shihab@gmail.com",
     *           "first_name": null,
     *           "last_name": null,
     *           "contact_no": "01712345678",
     *           "date_of_birth": null,
     *           "gender": null,
     *           "address": null
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
     *       @OA\Property(property="message", type="string",example="Unauthorized."),
     *     )
     *   ),
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     **/
    public function show(Request $request): JsonResponse
    {
        return $this->sendResponse($request->user(), 'User detail retrieved successfully.', ResponseAlias::HTTP_OK);
    }

    /**
     * update
     * @OA\Post (
     *   path="/api/v1/user",
     *   tags={"User"},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object", required={},
     *         @OA\Property(property="_method", type="string", example="PUT"),
     *         @OA\Property(property="name", type="string", example=""),
     *         @OA\Property(property="first_name", type="string", example=""),
     *         @OA\Property(property="last_name", type="string", example=""),
     *         @OA\Property(property="contact_no", type="string", example=""),
     *         @OA\Property(property="address", type="string", example=""),
     *         @OA\Property(property="date_of_birth", type="string",format="date", example=""),
     *         @OA\Property(property="gender", type="string", enum={"Male","Female","Other"}, example=""),
     *         @OA\Property(property="email", type="string", example=""),
     *         @OA\Property(property="password", type="string", example=""),
     *         @OA\Property(property="new_password", type="string", example=""),
     *         @OA\Property(property="confirm_password", type="string", example="")
     *       ),
     *     ),
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         type="object", required={"name", "email"},
     *         @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="first_name", type="string", example="John"),
     *         @OA\Property(property="last_name", type="string", example="Doe"),
     *         @OA\Property(property="contact_no", type="string", example="1234567890"),
     *         @OA\Property(property="address", type="string", example="123 Main St"),
     *         @OA\Property(property="date_of_birth", type="string", format="date", example="2000-01-01"),
     *         @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male"),
     *         @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *         @OA\Property(property="password", type="string", example="password123"),
     *         @OA\Property(property="new_password", type="string", example="newpassword123"),
     *         @OA\Property(property="confirm_password", type="string", example="newpassword123")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User Updated successfully"),
     *       @OA\Property(property="data", type="object", example={}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(
     *         property="data", type="object",
     *         @OA\Property(
     *           property="email", type="array",
     *           @OA\Items(type="string",example="The email has already been taken.")
     *         ),
     *       ),
     *     ),
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
     *   security={
     *     {"sanctum": {}}
     *   }
     * )
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();

        if($request->has('new_password') || $request->has('password') ) {
            if( ! Hash::check($request->get('password'), $user->password)){
                return $this->sendError('Unauthorised.', ['error'=>'Wrong Password'], ResponseAlias::HTTP_UNAUTHORIZED);
            }
            if($request->filled('new_password')){
                $user->password = Hash::make( $request->input('new_password') );
            }
        }

        $user->update( $request->except('password') );

        return $this->sendResponse($user, 'User detail updated successfully.', ResponseAlias::HTTP_OK);
    }

    public function redirect($provider): JsonResponse
    {
        $data['url'] = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return $this->sendResponse($data, 'Google redirect link', ResponseAlias::HTTP_OK);
    }

    public function callback(Request $request,$provider): JsonResponse
    {
        $user = Socialite::driver($provider)->stateless()->user();
        return $this->sendResponse($user, 'User logged in successfully.', ResponseAlias::HTTP_OK);
    }


    /**
     * social Login with code
     * @OA\Post (
     *   path="/api/v1/social/login",
     *   tags={"User"},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         required= {"provider_name","access_token"},
     *         @OA\Property( property="provider_name", type="string", example=""),
     *         @OA\Property( property="access_token", type="string",example="")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User logged in successfully"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         example={
     *           "token":"19|72aHg45WwAOXiR7pzAqjgG0k24mOWseGwv3iszRP4cc5ffa0"
     *         }
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Unauthorized."),
     *       @OA\Property(
     *         property="data", type="object",
     *         @OA\Property(property="error", type="string", example=""),
     *       ),
     *     ),
     *   )
     * )
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $provider = $request->input('provider_name');
        $token = $request->input('access_token');
        // get the provider's user. (In the provider server)
        $providerUser = Socialite::driver($provider)->userFromToken($token);
        // check if access token exists etc..
        // search for a user in our server with the specified provider id and provider name
        $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();
        // if there is no record with these data, create a new user
        if($user == null){
            $user = User::create([
                'provider_name' => $provider,
                'provider_id' => $providerUser->id,
                'name' => $providerUser->name,
                'email' => $providerUser->email,
                'password' => Hash::make(Str::random(8)),
            ]);
        }
        // create a token for the user, so they can login
        $token = $user->createToken('Authentication')->plainTextToken;
        // return the token for usage
        return $this->sendResponse($token, 'User logged in successfully.', ResponseAlias::HTTP_OK);
    }
}
