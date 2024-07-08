<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Banks;
use App\Models\Order;
use App\Utils\helper;
use App\Models\Result;
use App\Models\Program;
use App\Models\InterestCalc;
use Illuminate\Http\Request;
use App\Http\Traits\HelperApi;
use App\Models\Program_period;
use App\Services\ExamServices;
use App\Models\ContractProgram;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Notifications;
use App\Models\orderContract;
use App\Models\orderInstallment;
use App\Models\orderStatus;
use App\Models\PeroidGlobel;
use App\Models\ProgramTypes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApisController extends Controller
{
    use HelperApi;


    // public function contract($id)
    // {
    //     $data = ContractProgram::find($id);
    //     if ($data) {
    //         $transformedData = helper::transformDataByLanguage($data->toArray());
    //         return $this->onSuccess(200, 'found', $transformedData);
    //     } else {
    //         return $this->onSuccess(200, 'Not Found',$data);
    //     }
    // }




    public function getUser()
    {
        $user = auth('api')->user();
        if($user){
            return $this->onSuccess(200, 'found', $user);

        }else{
            return $this->onError(500, 'notfound', $user);

        }
    }

    public function upload_file(Request $request){
            // Base rules for all inputs
            $rules = [
                'name'=>['required','string'],
                'image' => ['required'], // Validate image and size limit to 1MB

            ];

            $customMessages = [
                'name.required' => __('validation.custom.name.required'),
                'image.required' => __('validation.custom.image.required'),
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
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $directory = $request->name; // Change this to your desired directory name
                $request->image->move(public_path($directory), $imageName);
                $imagePath = $directory . '/' . $imageName;
                return $this->onSuccess(200, 'uploaded', $imagePath);
            } else {
                return $this->onSuccess(500, __('validation.custom.not_avalable'));
            }


    }




}
