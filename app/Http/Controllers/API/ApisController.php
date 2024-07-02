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

    public function interest_calc()
    {
        $data = InterestCalc::select('interest_calcs.*')
            ->join('peroid_globels', 'interest_calcs.period_globle_id', '=', 'peroid_globels.id')
            ->orderBy('peroid_globels.num_months')
            ->with('period')
            ->get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);

        return $this->onSuccess(200, 'found', $transformedData);
    }
    public function programs()
    {
        $data = Program::with('program_type')->get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);

        return $this->onSuccess(200, 'found', $transformedData);
    }
    public function program_details($id)
    {
        $data = Program::with(['program_type', 'periods.period', 'fields.field', 'contracts' => function ($query) {
            $query->select('id', 'program_id', 'title_ar', 'title_en');
        }])->where('id', $id)->first();
        if ($data) {
            $transformedData = helper::transformDataByLanguage($data->toArray());
            return $this->onSuccess(200, 'found', $transformedData);
        } else {
            return $this->onSuccess(200, 'Not Found',$data);
        }
    }
    // public function program_contract($id)
    // {
    //     $data = Program::with(['contracts' => function ($query) {
    //         $query->select('id', 'program_id', 'title_ar','title_en');
    //     }])->find($id)->toArray();
    //     $transformedData = helper::transformDataByLanguage($data);
    //     return $this->onSuccess(200,'found',$transformedData);
    // }

    public function contract($id)
    {
        $data = ContractProgram::find($id);
        if ($data) {
            $transformedData = helper::transformDataByLanguage($data->toArray());
            return $this->onSuccess(200, 'found', $transformedData);
        } else {
            return $this->onSuccess(200, 'Not Found',$data);
        }
    }

    public function Banks()
    {
        $data = Banks::get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);
        return $this->onSuccess(200, 'found', $transformedData);
    }
    ////////////////order

    public function order_store(Request $request)
    {
        // Base rules for all inputs

        $rules = [
            'full_account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'peroid_globle' => ['required', 'exists:program_periods,period_globel_id'],
            'bank_id' => ['required', 'exists:banks,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'contracts' => ['required']

        ];
        $userId = helper::customer_id();



        $customMessages = [
            'full_account_name.required' => __('validation.custom.full_account_name.required'),
            'full_account_name.string' => __('validation.custom.full_account_name.string'),
            'full_account_name.max' => __('validation.custom.full_account_name.max'),
            'account_number.required' => __('validation.custom.account_number.required'),
            'account_number.string' => __('validation.custom.account_number.string'),
            'account_number.max' => __('validation.custom.account_number.max'),
            'peroid_globle.required' => __('validation.custom.peroid_globle.required'),
            'peroid_globle.exists' => __('validation.custom.peroid_globle.exists'),
            'bank_id.required' => __('validation.custom.bank_id.required'),
            'bank_id.exists' => __('validation.custom.bank_id.exists'),
            'program_id.required' => __('validation.custom.program_id.required'),
            'program_id.exists' => __('validation.custom.program_id.exists'),
            'contracts.required' => __('validation.custom.contracts.required'),
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
        $program = Program::find($request->program_id);
        $program_peroid = Program_period::where('program_id', '=', $request->program_id)->where('period_globel_id', $request->peroid_globle)->first();
        if ($program && $program_peroid) {
            DB::beginTransaction();
            try {
                $interest = $program->value * $program_peroid->percent / 100;
                $order = new Order();
                $order->total = $program->value + $interest;
                $order->subtotal = $program->value;
                $order->interest = $program_peroid->percent;
                $order->program_details = json_encode($program);
                $order->full_account_name = $request->full_account_name;
                $order->account_number = $request->account_number;
                $order->customer_id = $userId;
                $order->order_status = 1;
                $order->peroid_globle = $request->peroid_globle;
                $order->bank_id = $request->bank_id;
                $order->program_type_id = $program->program_type_id;

                $order->save();
                $contracts = json_decode($request->contracts, true); // Decoding as an associative array

                if (is_null($contracts)) {
                    return response()->json(['error' => 'Invalid JSON format for contracts'], 400);
                }

                foreach ($contracts as $data) {
                    if (!is_array($data) || !isset($data['id']) || !isset($data['signature'])) {
                        continue; // Skip invalid contract data
                    }

                    $order_contracts = new OrderContract();
                    $order_contracts->order_id = $order->id;
                    $order_contracts->contract_id = $data['id'];
                    $order_contracts->signature = $data['signature'];
                    $order_contracts->save();
                }
                $not = Notifications::create([
                    'customer_id' => $userId,
                    'title_ar' => 'تم انشاء الطلب جارى المراجعه',
                    'title_en' => 'The request has been created and is being reviewed',
                    'logo' => asset('images/1715301529.png'),
                    'description_ar' => 'تم انشاء الطلب جارى المراجعه',
                    'description_en' => 'The request has been created and is being reviewed',
                    'type' => 'order',
                    'related' => $order->id
                ]);


                DB::commit();
                return $this->onSuccess(200, __('messages.general.success_order'), true);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->onError(500, __('messages.general.some_error'));
            }
        } else {
            return $this->onError(500, __('messages.general.invalid_program'));
        }
    }


    public function program_type()
    {
        $data = ProgramTypes::get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);
        return $this->onSuccess(200, 'found', $transformedData);
    }
    public function status_order()
    {
        $data = orderStatus::get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);
        return $this->onSuccess(200, 'found', $transformedData);
    }

    public function filter()
    {
        $orderStatus = orderStatus::get()->toArray();
        $programTypes = ProgramTypes::get()->toArray();
        $transformedData = helper::transformDataByLanguage(['status' => $orderStatus, 'program_type' => $programTypes]);
        return $this->onSuccess(200, 'found', $transformedData);
    }

    public function getUser()
    {
        $user = auth('api')->user();
        if($user){
            return $this->onSuccess(200, 'found', $user);

        }else{
            return $this->onError(500, 'notfound', $user);

        }
    }



    public function all_orders(Request $request)
    {
        $userId = helper::customer_id();

        $query = Order::where('customer_id', $userId)
            ->with(['period', 'status', 'bank', 'program_type']);

        if (isset($request->program_type_id) && $request->program_type_id != null) {
            $query->where('program_type_id', $request->program_type_id);
        }

        if (isset($request->order_status) &&  $request->order_status != null) {
            $query->where('order_status', $request->order_status);
        }

        if (isset($request->sort) &&  $request->sort != null) {
            $query->orderBy('created_at', $request->sort);
        }

        $data = $query->get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);
        return $this->onSuccess(200, 'found', $transformedData);
    }

    public function last_order()
    {
        $userId = helper::customer_id();

        $query = Order::where('customer_id', $userId)
            ->with(['period', 'status', 'bank', 'program_type']);

        $data = $query->get()->last();
        if ($data) {
            $transformedData = helper::transformDataByLanguage($data->toArray());
            return $this->onSuccess(200, 'found', $transformedData);
        } else {
            return $this->onSuccess(200, 'Not Found',$data);
        }
    }

    public function conter_nota()
    {
        $userId = helper::customer_id();

        $count = Notifications::where('seen', 0)->where('customer_id', $userId)->count();
        return $this->onSuccess(200, 'found', $count);
    }

    public function all_nota($status = null)
    {
        $userId = helper::customer_id();
        $query = Notifications::where('customer_id', $userId);

        if ($status == '0' || $status == '1') {
            $query->where('seen', '=', $status);
        }
        $data = $query->get()->toArray();
        $transformedData = helper::transformDataByLanguage($data);

        return $this->onSuccess(200, 'found', $transformedData);
    }

    public function seen($id)
    {
        $data = Notifications::find($id);
        $data->seen = 1;
        $data->update();
        return $this->onSuccess(200, 'done');
    }
    /////remove
    public function order_compleated($id)
    {
        $order = Order::find($id);
        $peroid = PeroidGlobel::find($order->peroid_globle);

        if (!$order || !$peroid) {
            return response()->json(['error' => 'Order or period not found.'], 404);
        }
        $order->order_status=2;
        $order->save();

        $installmentValue = $order->total / $peroid->num_months;

        for ($i = 1; $i <= $peroid->num_months; $i++) {
            $installmentDate = now()->addMonths($i);

            DB::table('order_installments')->insert([
                'piad_date' => $installmentDate,
                'value' => $installmentValue,
                'status' => 0, // Unpaid
                'order_id' => $order->id,
                'parent_id' => 0,
                'type' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Installments created successfully.'], 201);
    }
    public function orders_installments(){
        $userId = helper::customer_id();

        $orderInstallments = Order::where('customer_id', $userId)
        ->with(['installments' => function ($query) {
            $query->where('status', 0);
        }])
        ->get()
        ->pluck('installments')
        ->flatten()
        ->groupBy('piad_date');
        return $this->onSuccess(200, 'done',$orderInstallments);

    }
    public function order_installments($id){
        $userId = helper::customer_id();
        $orderInstallments = Order::where('customer_id', $userId)
        ->where('id', $id)
        ->with(['installments'])
        ->get()
        ->pluck('installments')
        ->flatten();
        return $this->onSuccess(200, 'done',$orderInstallments);
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
    public function remove_order($id){
        $userId = helper::customer_id();

        $order = Order::where('id', $id)
                      ->where('customer_id', $userId)
                      ->where('order_status', 1)
                      ->first();

        if ($order) {
            try {
                orderInstallment::where('order_id', $order->id)->delete();
                orderContract::where('order_id', $order->id)->delete();
                $order->delete();
                return $this->onSuccess(200, 'Successfully deleted');
            } catch (\Exception $e) {
                return $this->onError(500, 'Failed to delete the order');
            }
        } else {
            return $this->onError(500, 'Cannot delete the order');
        }
    }

}
