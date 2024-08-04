<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserCustomer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class UserCustomerController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            // 'user_id' => 'required|exists:users,id',
            'business_type' => 'required|in:business,individual',
            'user_customer_type' => 'required|in:customer,supplier',
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'company_name' => 'required|string',
            'mail' => 'required|email',
            'phone' => 'required|numeric',
            'work' => 'required|string',
            'other_details' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'company_address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|numeric|digits:6',
        ]);

        // Create a new user_customer using Eloquent
        $userCustomer = new UserCustomer();
        $userCustomer->user_id = $user->id;
        $userCustomer->customer_type = $request->input('business_type');
        $userCustomer->user_customer_type = $request->input('user_customer_type');
        $userCustomer->first_name = $request->input('first_name');
        $userCustomer->second_name = $request->input('second_name');
        $userCustomer->company_name = $request->input('company_name');
        $userCustomer->mail = $request->input('mail');
        $userCustomer->phone = $request->input('phone');
        $userCustomer->work = $request->input('work');
        $userCustomer->other_details = $request->input('other_details') ?? NULL;
        $userCustomer->gst_no = $request->input('gst_no') ?? NULL;
        $userCustomer->company_address = $request->input('company_address') ?? NULL;
        $userCustomer->city = $request->input('city') ?? NULL;
        $userCustomer->state = $request->input('state') ?? NULL;
        $userCustomer->zip_code = $request->input('zip_code') ?? NULL;
        $userCustomer->save();

        return response()->json(['status' => 1, 'message' => 'UserCustomer created successfully', 'data' => $userCustomer], 201);
    }


    public function update(Request $request, $id)
    {
        $user = $request->user();
        $request->validate([
            // 'user_id' => 'exists:users,id',
            'business_type' => 'in:business,individual',
            'user_customer_type' => 'in:customer,supplier',
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'company_name' => 'required|string',
            'mail' => 'required|email',
            'phone' => 'required|numeric',
            'work' => 'required|string',
            'other_details' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'company_address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|numeric|digits:6',
        ]);

        $userCustomer =  UserCustomer::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if ($userCustomer) {
            // $userCustomer->user_id = $request->input('user_id',  $user->id);
            $userCustomer->customer_type = $request->input('business_type', $userCustomer->customer_type);
            $userCustomer->user_customer_type = $request->input('user_customer_type', $userCustomer->user_customer_type);
            $userCustomer->first_name = $request->input('first_name');
            $userCustomer->second_name = $request->input('second_name');
            $userCustomer->company_name = $request->input('company_name');
            $userCustomer->mail = $request->input('mail');
            $userCustomer->phone = $request->input('phone');
            $userCustomer->work = $request->input('work');
            $userCustomer->other_details = $request->input('other_details') ?? null;
            $userCustomer->gst_no = $request->input('gst_no') ?? null;
            $userCustomer->company_address = $request->input('company_address') ?? null;
            $userCustomer->city = $request->input('city') ?? null;
            $userCustomer->state = $request->input('state') ?? null;
            $userCustomer->zip_code = $request->input('zip_code') ?? null;
            $userCustomer->save();

            return response()->json(['status' => 1, 'message' => 'UserCustomer updated successfully', 'data' => $userCustomer], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'UserCustomer not find Wrong Id'], 404);
        }
    }


    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $userCustomer = UserCustomer::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if ($userCustomer) {
            $userCustomer->delete();
            return response()->json(['status' => 1, 'message' => 'UserCustomer deleted successfully'], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'UserCustomer Data Not Find'], 404);
        }
    }


    public function getCustomerData(Request $request)
    {
        $user = $request->user();
        $allUserId = [];

        if (empty($user->user_added_by)) {
            $user_added_by = $user->id;
            $allUserId[] = $user->id;
        } else {
            $user_added_by = $user->user_added_by;
        }
        $allUser = User::where('user_added_by', $user_added_by)->get();
        foreach ($allUser as $user_all) {
            $allUserId[] = $user_all->id;
        }
        
        $perPage = $request->input('per_page', 10);
        $supplierData = UserCustomer::where('user_customer_type', 'customer')
        ->whereIn('user_id', $allUserId)
            ->paginate($perPage);
        $supplierData->appends(['per_page' => $perPage]);

        return response()->json(['status' => 1, 'supplierData' => $supplierData], 200);
    }


    public function getSupplierData(Request $request)
    {
        $user = $request->user();

        $allUserId = [];
        if (empty($user->user_added_by)) {
            $user_added_by = $user->id;
            $allUserId[] = $user->id;
        } else {
            $user_added_by = $user->user_added_by;
        }
        $allUser = User::where('user_added_by', $user_added_by)->get();
        foreach ($allUser as $user_all) {
            $allUserId[] = $user_all->id;
        }

        $perPage = $request->input('per_page', 10);
        $supplierData = UserCustomer::where('user_customer_type', 'supplier')
            ->whereIn('user_id', $allUserId)
            ->paginate($perPage);
        $supplierData->appends(['per_page' => $perPage]);

        return response()->json(['status' => 1, 'supplierData' => $supplierData], 200);
    }

}
