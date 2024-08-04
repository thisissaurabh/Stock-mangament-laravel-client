<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class ExtraDataController extends Controller
{

    public function getExpensean(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 400);
        }

        $perPage = $request->input('per_page', 15);
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;
        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        $expenses = Expense::whereIn('user_id', $allUserId)->paginate($perPage);

        if ($expenses->isEmpty()) {
            return response()->json(['status' => 0, 'message' => 'No expenses found'], 404);
        } else {
            return response()->json(['status' => 1, 'message' => 'Expenses list', 'data' => $expenses], 200);
        }
    }


    public function expancestore(Request $request)
    {
        $user = $request->user();
        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;

        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable',
        ]);

        $expense = new Expense();
        $expense->user_id = $user->id;
        $expense->title = $request->title;
        $expense->amount = $request->amount;
        $expense->date = $request->date;
        $expense->description = $request->description;
        $expense->save();
        return response()->json(['status' => 1, 'message' => 'Expense created successfully!', 'data' => $expense], 201);
    }


    public function deleteExpensean(Request $request, $expenseId)
    {
        $user = $request->user();

        if ($user->role == 'admin') {
            $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;
            $allUser = User::where('user_added_by', $user_added_by)->get();
            $allUserId = $allUser->pluck('id')->push($user->id)->toArray();
            $expense = Expense::find($expenseId);
            if ($expense) {
                if (in_array($expense->user_id, $allUserId)) {
                    $expense->delete();
                    return response()->json(['status' => 1, 'message' => 'Expense deleted successfully!'], 200);
                } else {
                    return response()->json(['status' => 0, 'message' => 'You are not authorized to delete this expense'], 403);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Expense not found'], 404);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'You are not authorized to perform this action'], 401);
        }
    }
}
