<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class SalesPersonController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;

        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        $salesPersons = SalesPerson::whereIn('user_id', $allUserId)->get();

        $salesPersons = $salesPersons->map(function ($salesPerson) {
            $salesPerson->photo = asset($salesPerson->photo);
            return $salesPerson;
        });
        if ($salesPersons->isEmpty()) {

            return response()->json(['status' => 0, 'message' => 'No sales person found'], 404);
        } else {
            return response()->json(['status' => 1, 'message' => 'Sales persons list', 'data' => $salesPersons], 200);
        }
    }

    public function store(Request $request)
    {

        $user = $request->user();
        if ($user->role == 'admin') {
            $user_id = $user->id;

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'nullable|email',
                'phone' => 'required|numeric|digits_between:10,12',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Check for validation failure
            if ($validator->fails()) {
                return response()->json(['status' => 0, 'errors' => $validator->errors()], 400);
            }

            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('adminProfile/salesPerson'), $imageName);

                $photoPath =  '/adminProfile/salesPerson/' . $imageName;
            } else {
                $photoPath = null;
            }

            $salesPerson = SalesPerson::create([
                'user_id' => $user_id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'photo' => $photoPath,
            ]);

            return response()->json(['status' => 1, 'message' => 'Sales person created successfully!', 'data' => $salesPerson], 201);
        } else {
            return response()->json(['status' => 0, 'message' => 'You are not authorized to perform this action'], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role == 'admin') {
            $salesPerson = SalesPerson::find($id);
            if ($salesPerson) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'nullable|email',
                    'phone' => 'required|numeric|digits_between:10,12',
                    'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                // Check for validation failure
                if ($validator->fails()) {
                    return response()->json(['status' => 0, 'errors' => $validator->errors()], 400);
                }
                if ($request->hasFile('photo')) {

                    if ($salesPerson->photo) {
                        $oldImagePath = public_path($salesPerson->photo);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $image = $request->file('photo');
                    $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('adminProfile/salesPerson'), $imageName);
                    $photoPath =  '/adminProfile/salesPerson/' . $imageName;
                    $salesPerson->photo = $photoPath;
                }
                $salesPerson->name = $request->name;
                $salesPerson->email = $request->email;
                $salesPerson->phone = $request->phone;
                $salesPerson->save();
                return response()->json(['status' => 1, 'message' => 'Sales person updated successfully!', 'data' => $salesPerson], 201);
            } else {
                return response()->json(['status' => 0, 'message' => 'Sales person not found'], 404);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'You are not authorized to perform this action'], 401);
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;

        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        $salesPerson = SalesPerson::find($id);

        if ($salesPerson) {

            if (in_array($salesPerson->user_id, $allUserId)) {
                $salesPerson->delete();
                return response()->json(['status' => 1, 'message' => 'Sales person deleted successfully!'], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'You are not authorized to delete this sales person'], 403);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Sales person not found'], 404);
        }
    }
}
