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
use App\Models\Account;
use App\Models\Customer;
use App\Models\Notifications;
use App\Models\orderContract;
use App\Models\orderInstallment;
use App\Models\orderStatus;
use App\Models\PaymentRequest;
use App\Models\Payments;
use App\Models\PeroidGlobel;
use App\Models\ProgramTypes;
use App\Models\Transactions;
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

    //// account
    public function create_account(Request $request)
    {
        $rules = [
            'currancy_id' => ['required', 'exists:currancies,id'],
        ];

        $customMessages = [

            'currancy_id.required' => __('validation.custom.currancy.required'),
            'currancy_id.exists' => __('validation.custom.currancy.exists'),
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            // Collect all error messages
            $errorMessages = $validator->errors()->all();

            // Combine all messages into a single string
            $combinedErrorMessage = implode(' ', $errorMessages);

            return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
        }

        Account::create([
            'customer_id' => helper::customer_id(),
            'currancy_id' => $request->currancy_id,
            'balance' => false,

        ]);
        return $this->onSuccess(200, 'success_create_account');
    }

    public function save_payment_request(Request $request)
    {

        $rules = [
            'payment_id' => ['required', 'exists:payments,id'],
            'value' => ['required', 'numeric', 'min:1'],
        ];
        $paymentType = $this->get_type_payment($request->payment_id);
        if ($paymentType === 'not found') {
            return $this->onError(422, 'not found payment');
        }

        $customMessages = [
            'payment_id.required' => __('validation.custom.payment.required'),
            'payment_id.exists' => __('validation.custom.payment.exists'),
        ];

        if ($paymentType->type == 1) {
            $rules['details_offline'] = ['required'];
            $customMessages = array_merge($customMessages, [
                'details_offline.required' => __('validation.custom.details_offline.required'),
            ]);
        } else {
            /////////////payment online

        }
        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            // Collect all error messages
            $errorMessages = $validator->errors()->all();

            // Combine all messages into a single string
            $combinedErrorMessage = implode(' ', $errorMessages);
            return $this->onError(422, $combinedErrorMessage, __('messages.general.validation_error'));
        }
        $account = helper::get_account($request->header('account_id'));
        if ($account == false) {
            return $this->onError(422, 'not found account');
        }
        DB::beginTransaction();
        try {
            PaymentRequest::create([
                'account_id'=>$account->id,
                'value'=>$request->value,
                'payment_id'=>$request->payment_id,
                'details_offline'=>json_encode($request->details_offline),
                'feas'=>$paymentType->feas
            ]);
            Transactions::create([
                'account_id'=>$account->id,
                'value'=>$request->value,
                'transactions_status_id'=>3
            ]);



            DB::commit();
            return $this->onSuccess(200, __('messages.general.success_payment_request'), true);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->onError(500, __('messages.general.some_error'));
        }

        return $this->onSuccess(200, 'success_payment_request');
    }
    private function get_type_payment($payment_id)
    {
        $payment = Payments::find($payment_id);
        return $payment ? $payment : 'not found';
    }




    public function getUser()
    {
        $user = auth('api')->user();
        if ($user) {
            return $this->onSuccess(200, 'found', $user);
        } else {
            return $this->onError(500, 'notfound', $user);
        }
    }

    public function upload_file(Request $request)
    {
        // Base rules for all inputs
        $rules = [
            'name' => ['required', 'string'],
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
