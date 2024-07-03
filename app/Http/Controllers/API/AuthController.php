<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\OTP;
use App\Utils\helper;
use App\Models\ageRange;
use App\Models\Customer;
use App\Models\otp_register;
use Illuminate\Http\Request;
use App\Http\Traits\HelperApi;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Traits\ImageProcessing;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


class AuthController extends Controller
{
    use HelperApi, ImageProcessing;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'changepass', 'verifyOtp_for_change_pass', 'forgetpassword', 'get_age_range','verifyOtpForRegister','checphone']]); //login, register methods won't go through the api guard
    }

    public function login(Request $request)
    {
             // Determine if the input is an email or phone and adjust rules accordingly
             $rules = [
                'password' => ['required'],
                'email' => [
                    'required'
                    , 'email', 'max:255', 'unique:customers,email',


                ],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // Collect all error messages
                $errorMessages = $validator->errors()->all();

                // Combine all messages into a single string
                $combinedErrorMessage = implode(' ', $errorMessages);

                return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
            }

        try {

            $user = Customer::where('email', $request->phone)->first();

            if ($user && (!Hash::check($request->password, $user->password))) {
                return $this->onError(401, __('messages.general.invalid_data'));
            }


            if (!$user) {
                return $this->onError(401, __('messages.general.invalid_data'));
            }

            $token = JWTAuth::fromUser($user);

            if (!$token) {

                return $this->onError(401, __('messages.general.unauthorized'));
            }
            return $this->onSuccessWithToken(200, __('messages.general.login'), $user, $token);
        } catch (\Throwable $error) {
            return $this->onError(500, __('messages.general.server_error'), $error->getMessage());
        }
    }


    public function register(Request $request)
    {
        try {

            // Base rules for all inputs
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
                'phone' => [
                    'required',
                    'numeric',
                    'regex:/^(5|6|9)[0-9]{7}$/',
                    'unique:customers,phone'
                ],
                'password' => ['required', 'string', 'min:6'],
                'avtar' => ['nullable','string','max:300'], // Updated to validate image and size limit to 1MB
                'image_id_front' => ['nullable','string','max:300'], // Updated to validate image and size limit to 1MB
                'image_id_back' => ['nullable','string','max:300'], // Updated to validate image and size limit to 1MB
                'birthday' => ['nullable', 'date'],
            ];

            $customMessages = [
                'name.required' => __('validation.custom.name.required'),
                'name.string' => __('validation.custom.name.string'),
                'name.max' => __('validation.custom.name.max'),

                'email.nullable' => __('validation.custom.email.required'),
                'email.email' => __('validation.custom.email.email'),
                'email.max' => __('validation.custom.email.max'),
                'email.unique' => __('validation.custom.email.unique'),
                'phone.required' => __('validation.custom.phone.required'),
                'phone.numeric' => __('validation.custom.phone.numeric'),
                'phone.regex' => __('validation.custom.phone.regex'),
                'phone.unique' => __('validation.custom.phone.unique'),
                'password.required' => __('validation.custom.password.required'),
                'password.string' => __('validation.custom.password.string'),
                'password.min' => __('validation.custom.password.min'),
                'address.nullable' => __('validation.custom.address.required'),
                'address.string' => __('validation.custom.address.string'),
                'address.max' => __('validation.custom.address.max'),
                'avtar.max' => __('validation.custom.logo.max'), // Change from 'logo.image' to 'logo.max'
                'image_id_front.max' => __('validation.custom.image_id_front.max'), // Change from 'image_id_front.image' to 'image_id_front.max'
                'image_id_back.max' => __('validation.custom.image_id_back.max'), // Change from 'image_id_back.image' to 'image_id_back.max'
                'birthday.nullable' => __('validation.custom.birthday.required'),
                'birthday.date' => __('validation.custom.birthday.date'),
            ];



            // Perform validation
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) {
                // Collect all error messages
                $errorMessages = $validator->errors()->all();

                // Combine all messages into a single string
                $combinedErrorMessage = implode(' ', $errorMessages);

                return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
            }

            // Handle image upload



            // Create user
            $user = Customer::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'avtar' => $request->avtar,
                'image_id_back' =>  $request->image_id_back,
                'image_id_front' => $request->image_id_front,
                'birthday' => $request->birthday,
                'is_verified' => 0,
                'status' => 0,
            ]);

            event(new Registered($user));


            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            return $this->onSuccessWithToken(200, __('messages.general.create_user'), $user, $token);
        } catch (\Throwable $error) {
            return $this->onError(500, __('messages.general.server_error'), $error->getMessage());
        }
    }




    public function logout(Request $request)
    {
        try {
            // Invalidate the token by adding it to the blacklist
            JWTAuth::parseToken()->invalidate();
            return $this->onSuccess(200, __('messages.general.logout_success'));
        } catch (JWTException $e) {
            return $this->onError(500, __('messages.general.logout_error'));
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 400);
                return $this->onError(400, 'Token not provided');
            }

            $refreshedToken = JWTAuth::refresh($token);
            return $this->onSuccessWithToken(200, 'success change token', null, $refreshedToken);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->onError(401, 'Token is invalid');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->onError(500, 'Could not refresh token');
        }
    }
    public function forgetpassword(Request $request)
    {
        // Determine if the input is an email or phone and adjust rules accordingly
        $rules = [
            'password' => ['required', 'min:6', 'confirmed'],
            'email' => [
                'required'
                , 'email', 'max:255', 'unique:customers,email',

            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Collect all error messages
            $errorMessages = $validator->errors()->all();

            // Combine all messages into a single string
            $combinedErrorMessage = implode(' ', $errorMessages);

            return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
        }

        $customer= Customer::where('email', $request->email)->first();

        if (!$customer) {
            return $this->onError(404, __('messages.general.user_notfound'));
        }
        $customer->password = Hash::make($request->password);
        $customer->is_verified = true;

        $customer->save();
        return $this->onSuccess(200, __('messages.general.change_pass'), true);
        // Perform validation
    }



    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return $this->onSuccess(200, __('messages.Email_verified'), true);

    }

    public function resend(Request $request)
    {
        $customer = $request->user();

        if ($customer->hasVerifiedEmail()) {
            return response()->json(['message' => __('messages.Email_already_verified')], 400);
        }

        $customer->sendEmailVerificationNotification();

        return response()->json(['message' => __('messages.Verification_link_sent')], 200);
    }


    //////update profile

    // public function updateUser(Request $request)
    // {
    //     $userId = helper::customer_id();

    //     $user = Customer::find($userId);
    //     if (!$user) {
    //         return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
    //     }

    //     // Base rules for all inputs
    //     $rules = [
    //         'phone' => [
    //             'nullable',
    //             'numeric',
    //             'regex:/^(5|6|9)[0-9]{7}$/',
    //             'unique:customers,phone,' . $userId
    //         ],
    //         'password' => ['nullable', 'string', 'min:6'],
    //         'address' => ['nullable', 'string', 'max:255'],
    //         'avtar' => ['nullable','string','max:300'], // Validate image and size limit to 1MB
    //         'image_id_front' => ['required_with:image_id_back','string','max:300'], // Required if image_id_back is present, validate image and size limit to 1MB
    //         'image_id_back' => ['required_with:image_id_front','string','max:300'], // Required if image_id_front is present, validate image and size limit to 1MB
    //     ];

    //     $customMessages = [
    //         'phone.numeric' => __('validation.custom.phone.numeric'),
    //         'phone.regex' => __('validation.custom.phone.regex'),
    //         'phone.unique' => __('validation.custom.phone.unique'),
    //         'password.string' => __('validation.custom.password.string'),
    //         'password.min' => __('validation.custom.password.min'),
    //         'address.string' => __('validation.custom.address.string'),
    //         'address.max' => __('validation.custom.address.max'),
    //         'avtar.max' => __('validation.custom.avtar.max'),
    //         'image_id_front.required_with' => __('validation.custom.image_id_front.required_with'),
    //         'image_id_front.string' => __('validation.custom.image_id_front.string'), // Adjusted to 'string' from 'image'
    //         'image_id_front.max' => __('validation.custom.image_id_front.max'),
    //         'image_id_back.required_with' => __('validation.custom.image_id_back.required_with'),
    //         'image_id_back.string' => __('validation.custom.image_id_back.string'), // Adjusted to 'string' from 'image'
    //         'image_id_back.max' => __('validation.custom.image_id_back.max'),
    //     ];

    //     // Perform validation
    //     $validator = Validator::make($request->all(), $rules, $customMessages);
    //     if ($validator->fails()) {
    //         // Collect all error messages
    //         $errorMessages = $validator->errors()->all();

    //         // Combine all messages into a single string
    //         $combinedErrorMessage = implode(' ', $errorMessages);

    //         return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
    //     }

    //     $input = $request->only(['phone', 'address']);

    //     if ($request->has('phone') && $request->phone != null) {
    //         $user->phone = $request->phone;
    //         $user->is_verified = 0;
    //     }

    //     if ($request->has('address') && $request->address != null) {
    //         $user->address = $request->address;
    //     }

    //     if ($request->has('image_id_front') || $request->has('image_id_back')) {
    //         $user->is_approve_id = 0;
    //     }

    //     if ( $request->avtar !=null) {

    //         $user->avtar = $request->avtar;
    //     }

    //     if ($request->image_id_front !=null) {

    //         $user->image_id_front = $request->image_id_front;
    //     }

    //     if ($request->image_id_back !=null) {
    //       $user->image_id_back = $request->image_id_back;
    //     }

    //     // Handle password separately
    //     if ($request->filled('password')) {

    //         $user->password = bcrypt($request->password);
    //     }

    //     try {
    //         $user->save();

    //         return $this->onSuccess(200, 'User updated successfully', $user);
    //     } catch (Exception $e) {
    //         return $this->onError(500, $e->getMessage());
    //     }
    // }
    public function updateUser(Request $request)
    {
        $userId = helper::customer_id();

        $user = Customer::find($userId);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        // Base rules for all inputs
        $rules = [
            'phone' => [
                'nullable',
                'numeric',
                'regex:/^(5|6|9)[0-9]{7}$/',
                'unique:customers,phone,' . $userId
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'address' => ['nullable', 'string', 'max:255'],
            'avtar' => ['nullable','string','max:300'], // Validate image and size limit to 1MB
            'image_id_front' => ['required_with:image_id_back','string','max:300'], // Required if image_id_back is present, validate image and size limit to 1MB
            'image_id_back' => ['required_with:image_id_front','string','max:300'], // Required if image_id_front is present, validate image and size limit to 1MB
        ];

        $customMessages = [
            'phone.numeric' => __('validation.custom.phone.numeric'),
            'phone.regex' => __('validation.custom.phone.regex'),
            'phone.unique' => __('validation.custom.phone.unique'),
            'password.string' => __('validation.custom.password.string'),
            'password.min' => __('validation.custom.password.min'),
            'address.string' => __('validation.custom.address.string'),
            'address.max' => __('validation.custom.address.max'),
            'avtar.max' => __('validation.custom.avtar.max'),
            'image_id_front.required_with' => __('validation.custom.image_id_front.required_with'),
            'image_id_front.string' => __('validation.custom.image_id_front.string'), // Adjusted to 'string' from 'image'
            'image_id_front.max' => __('validation.custom.image_id_front.max'),
            'image_id_back.required_with' => __('validation.custom.image_id_back.required_with'),
            'image_id_back.string' => __('validation.custom.image_id_back.string'), // Adjusted to 'string' from 'image'
            'image_id_back.max' => __('validation.custom.image_id_back.max'),
        ];

        // Perform validation
        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            // Collect all error messages
            $errorMessages = $validator->errors()->all();

            // Combine all messages into a single string
            $combinedErrorMessage = implode(' ', $errorMessages);

            return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
        }
        $input = array_filter($request->all(), function ($value) {
            return !is_null($value);
        });

        if ($request->has('phone') && $request->phone != null && $request->phone != $user->phone) {
                $user->phone = $request->phone;
        }


        if ($request->filled('password')) {

            $user->password = bcrypt($request->password);
        }

        if ($request->has('image_id_front') || $request->has('image_id_back')) {
            $user->is_approve_id = 0;
        }
        try {
            $user->updateOrFail($input);
            return $this->onSuccess(200, 'User updated successfully', $user);
        } catch (Exception $e) {
            return $this->onError(500, $e->getMessage());
        }
    }
}
