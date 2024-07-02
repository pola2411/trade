<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\BanksUpdateRequest;
use App\Http\Requests\ProgramsTypesStoreRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BanksController extends Controller
{
    ///start types
    public function index()
    {

        return view('admin.banks.list');
    }
    public function getData()
    {
        $data = Banks::all();

        return response()->json([
            'data' => $data,
            'message' => 'found data'
        ]);
    }

    public function store(ProgramsTypesStoreRequest $request)
    {
        try {
            $requestData = $request->all();


            Banks::create($requestData);


            Toastr::success(__('Bank Created Successfully'), __('Success'));

            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error(__('Try Again'), __('Error'));
            return redirect()->back();
        }
    }

    public function update(BanksUpdateRequest $request)
    {
        try {
            // Find the category by ID
            $Peroid = Banks::findOrFail($request->input('id'));

            // Get the validated data, excluding 'id'
            $requestData = $request->except('id');

            // Update the Peroid with the validated data
            $Peroid->update($requestData);

            // Display success message and redirect to index
            Toastr::success(__('Bank Updated Successfully'), __('Success'));
            return redirect()->back();
        } catch (\Throwable $th) {
            // Display error message and redirect back
            Toastr::error(__('Try Again'), __('Error'));
            return redirect()->back();
        }
    }

    public function delete(Banks $Banks)
    {
        try {
            $Banks->delete(); // Soft delete the Category

            Toastr::success(__('Bank Deleted Successfully'), __('Success'));

            return redirect()->back();
        } catch (ModelNotFoundException $exception) {
            Toastr::error(__('Bank Not Found'), __('Error'));
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong. Please try again.'), __('Error'));
            return redirect()->back();
        }
    }
}
