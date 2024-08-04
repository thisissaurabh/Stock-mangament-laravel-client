<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\GroupBrand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Discount;

class GroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function getGroup(Request $request, $groupName = null)
    {
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;

        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        $query = Group::whereIn('user_id', $allUserId);

        if (!is_null($groupName)) {
            $operator = config('database.connections')[config('database.default')]['driver'] === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where('group_name', $operator, '%' . $groupName . '%');
        }
        $allGroups = $query->get();

        $allGroups->map(function ($group) {
            $groupBrands = GroupBrand::where('group_id', $group->id)->get();
            if (count($groupBrands) > 0) {
                $group->brand = $groupBrands;
            } else {
                $group->brand = null;
            }
            return $group;
        });


        return response()->json(['status' => 1, 'data' => $allGroups], 200);
    }

    public function storeGroup(Request $request)
    {

        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;

        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        $request->validate([
            'group_name' => [
                'required',
                Rule::unique('groups', 'group_name')->where(function ($query) use ($allUserId) {
                    return $query->whereIn('user_id', $allUserId);
                }),
            ],
            'hsn_sac_code' => 'nullable',
            'cgst' => 'nullable',
            'igst' => 'nullable',
            'sgst' => 'nullable',
            'cess' => 'nullable',
        ]);

        // return   $request->all();
        $group = new Group();
        $group->user_id = $user->id;
        $group->group_name = $request->group_name;
        $group->hsn_sac_code = $request->hsn_sac_code;
        $group->cgst = $request->cgst;
        $group->sgst = $request->sgst;
        $group->igst = $request->igst;
        $group->cess = $request->cess;
        $group->save();
        return response()->json(['status' => 1, 'message' => 'Group created successfully!', 'data' => $group], 201);
    }

    public function updateGroup(Request $request, $groupId)
    {
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;
        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();
        $request->validate([
            'group_name' => [
                'required',
                Rule::unique('groups', 'group_name')->where(function ($query) use ($allUserId) {
                    return $query->whereIn('user_id', $allUserId);
                })->ignore($groupId)
            ],
            'hsn_sac_code' => 'nullable',
            'cgst' => 'nullable',
            'igst' => 'nullable',
            'sgst' => 'nullable',
            'cess' => 'nullable',
        ]);

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['status' => 0, 'message' => 'Group not found'], 404);
        }
        $group->group_name = $request->group_name;
        $group->hsn_sac_code = $request->hsn_sac_code;
        $group->cgst = $request->cgst;
        $group->sgst = $request->sgst;
        $group->igst = $request->igst;
        $group->cess = $request->cess;
        $group->save();
        return response()->json(['status' => 1, 'message' => 'Group updated successfully!', 'data' => $group], 200);
    }

    public function deleteGroup($groupId)
    {

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['status' => 0, 'message' => 'Group not found'], 404);
        }
        $group->delete();
        return response()->json(['status' => 1, 'message' => 'Group deleted successfully!'], 200);
    }

    public function createGroupBrand(Request $request)
    {

        $request->validate([
            'group_id' => 'required',
            'brand_name' => [
                'required',
                Rule::unique('groups_brand')->where(function ($query) use ($request) {
                    return $query->where('group_id', $request->input('group_id'));
                }),
            ],
        ]);

        GroupBrand::create([
            'group_id' => $request->input('group_id'),
            'brand_name' => $request->input('brand_name'),
        ]);

        return response()->json(['status' => 1, 'message' => 'Group Brand created successfully!'], 201);
    }

    public function updateGroupBrand(Request $request, $id)
    {
        $request->validate([
            'group_id' => 'required',
            'brand_name' => [
                'required',
                Rule::unique('groups_brand')->where(function ($query) use ($request) {
                    return $query->where('group_id', $request->input('group_id'));
                })->ignore($id)
            ],
        ]);
        $groupBrand = GroupBrand::find($id);
        if (!$groupBrand) {
            return response()->json(['status' => 0, 'message' => 'Group Brand not found'], 404);
        }
        $groupBrand->group_id = $request->input('group_id');
        $groupBrand->brand_name = $request->input('brand_name');
        $groupBrand->save();
        return response()->json(
            ['status' => 1, 'message' => 'Group Brand updated successfully!', 'data' => $groupBrand],
            200
        );
    }

    public function deleteGroupBrand($id)
    {
        $groupBrand = GroupBrand::find($id);
        if (!$groupBrand) {
            return response()->json(['status' => 0, 'message' => 'Group Brand not found'], 404);
        }

        $groupBrand->delete();
        return response()->json(['status' => 1, 'message' => 'Group Brand deleted successfully!'], 200);
    }

    public function getAllGroupBrands($groupId, $brandName = null)
    {
        $groupBrands = GroupBrand::where('group_id', $groupId)
            ->when($brandName, function ($query) use ($brandName) {
                return $query->where('brand_name', 'LIKE', '%' . $brandName . '%');
            })->get();
        return response()->json(['status' => 1, 'message' => 'Group Brands retrieved successfully!', 'data' => $groupBrands], 200);
    }

    // discout

    public function getDiscount(Request $request)
    {
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;
        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();
        $discounts = Discount::whereIn('user_id', $allUserId)->get();
        if ($discounts->isEmpty()) {
            return response()->json(['status' => 0, 'message' => 'No discounts found'], 404);
        }
        return response()->json(['status' => 1, 'message' => 'Discounts list', 'data' => $discounts], 200);
    }


    public function storeDiscount(Request $request)
    {
        $user = $request->user();
        if ($user->role == 'admin') {

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:percentage,fixed_amount',
                'value' => 'required|numeric',
                'applies_to' => 'required|in:whole_cart,line_item',
                'starts_at' => 'required|date',
                'ends_at' => 'nullable|date|after:starts_at',
            ]);

            $discount = new Discount();
            $discount->name = $request->name;
            $discount->type = $request->type;
            $discount->value = $request->value;
            $discount->applies_to = $request->applies_to;
            $discount->user_id = $user->id;
            $discount->starts_at = $request->starts_at;
            $discount->ends_at = $request->ends_at;
            $discount->save();

            return response()->json(['status' => 1, 'message' => 'Discount created successfully!', 'data' => $discount], 201);
        } else {
            return response()->json(['status' => 0, 'message' => 'You are not authorized to perform this action'], 401);
        }
    }


    public function destroyDiscount(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role == 'admin') {

            $discount = Discount::where('user_id', $user->id)->find($id);
            if ($discount) {
                $discount->delete();
                return response()->json(['status' => 1, 'message' => 'Discount deleted successfully!'], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Discount not found'], 404);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'You are not authorized to perform this action'], 401);
        }
    }

    public function updateDiscount(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role == 'admin') {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'type' => 'required|in:percentage,fixed_amount',
                'value' => 'required|numeric',
                'applies_to' => 'required|in:whole_cart,line_item',
                'starts_at' => 'required|date',
                'ends_at' => 'nullable|date|after:starts_at',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'errors' => $validator->errors()], 400);
            }

            $discount = Discount::where('user_id', $user->id)->find($id);

            if ($discount) {
                $discount->name = $request->name;
                $discount->type = $request->type;
                $discount->value = $request->value;
                $discount->applies_to = $request->applies_to;
                $discount->starts_at = $request->starts_at;
                $discount->ends_at = $request->ends_at;
                $discount->save();
                return response()->json(['status' => 1, 'message' => 'Discount updated successfully!', 'data' => $discount], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Discount not found'], 404);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'You are not authorized to perform this action'], 401);
        }
    }
}
