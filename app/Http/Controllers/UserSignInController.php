<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use Laravel\Passport\Token;
use Illuminate\Support\Str;
use Defuse\Crypto\File;

class UserSignInController extends Controller
{


    // private function emailTemp($otp)
    // {

    //     $temp = '<body style="font-family: Helvetica, Arial, sans-serif; min-width: 1000px; overflow: auto; line-height: 2;">
    //            <div style="margin: 50px auto; width: 70%; padding: 20px 0;">
    //              <div style="border-bottom: 1px solid #eee;">
    //                  <a href="#" style="font-size: 1.4em; color: #00466a; text-decoration: none; font-weight: 600;">spycotech</a>
    //              </div>
    //              <p style="font-size: 1.1em;">Hi,</p>
    //              <p>Thank you for choosing spycotech. Use the following OTP to complete your Sign Up procedures. OTP is valid
    //                  for 5 minutes</p>
    //              <h2
    //                  style="background: #00466a; margin: 0 auto; width: max-content; padding: 0 10px; color: #fff; border-radius: 4px;">
    //                  ' . $otp . '</h2>
    //              <p style="font-size: 0.9em;">Regards,<br />spycotech</p>
    //              <hr style="border: none; border-top: 1px solid #eee;" />
    //              <div style="float: right; padding: 8px 0; color: #aaa; font-size: 0.8em; line-height: 1; font-weight: 300;">
    //                  <p>spycotech Inc</p>
    //                  <p>1600 Amphitheatre Parkway</p>
    //                  <p>Dripar</p>
    //              </div>
    //          </div>
    //          </body>';
    //     return $temp;
    // }

    private function emailTemp($otp, $action = 'Sign Up')
    {
        $temp = '<body style="font-family: Helvetica, Arial, sans-serif; min-width: 1000px; overflow: auto; line-height: 2;">
               <div style="margin: 50px auto; width: 70%; padding: 20px 0;">
                 <div style="border-bottom: 1px solid #eee;">
                     <a href="#" style="font-size: 1.4em; color: #00466a; text-decoration: none; font-weight: 600;">spycotech</a>
                 </div>
                 <p style="font-size: 1.1em;">Hi,</p>
                 <p>Thank you for choosing spycotech. Use the following OTP to complete your ' . $action . ' procedures. OTP is valid
                     for 5 minutes</p>
                 <h2
                     style="background: #00466a; margin: 0 auto; width: max-content; padding: 0 10px; color: #fff; border-radius: 4px;">
                     ' . $otp . '</h2>
                 <p style="font-size: 0.9em;">Regards,<br />spycotech</p>
                 <hr style="border: none; border-top: 1px solid #eee;" />
                 <div style="float: right; padding: 8px 0; color: #aaa; font-size: 0.8em; line-height: 1; font-weight: 300;">
                     <p>spycotech Inc</p>
                     <p>1600 Amphitheatre Parkway</p>
                     <p>Dripar</p>
                 </div>
             </div>
             </body>';
        return $temp;
    }


    public function sendEmailOtp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }

        $email = $request->input('email');
        $userWithEmail = User::where('email', $email)->first();

        if ($userWithEmail) {
            return response()->json(['status' => 0, 'message' => 'Email is already registered'], 422);
        }

        $otp = mt_rand(1000, 9999);
        Mail::send([], [], function ($message) use ($request, $otp) {
            $message->to($request->email)
                ->subject('Your OTP for verification')
                ->html($this->emailTemp($otp));
        });



        Session::put('otp', $otp);
        Session::put('email', $request->email);

        return response()->json(['status' => 1, 'message' => 'OTP sent successfully', 'otp' => $otp]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer|digits_between:4,4',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }
        $otpEntered = $request->input('otp');

        $otpInSession = Session::get('otp');
        $emailInSession = Session::get('email');

        if ($otpEntered == $otpInSession && $emailInSession) {
            Session::forget('otp');
            return response()->json(['status' => 1, 'message' => 'OTP verified successfully', 'email' => $emailInSession], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Invalid OTP'], 422);
        }
    }

    public function register(Request $request)
    {
        $emailInSession = Session::get('email');
        if (empty($emailInSession)) {
            return response()->json(['status' => 0, 'message' => 'User registration not alow'], 500);
        }

        $validatedData = $request->validate(['name' => 'required|string|max:50',
            'copanyName' => 'required|string|max:50',
            'userName' => 'required|string|max:20',
            'image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'gstNumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|numeric|digits:6',
        ]);

        if ($request->hasFile('image_url')) {
            $uploadedFile = $request->file('image_url');
            $imageName = time() . '.' . $uploadedFile->getClientOriginalExtension();
            $uploadedFile->move(public_path('adminProfile/userImage'), $imageName);
            $newImageName  = '/adminProfile/userImage/' . $imageName;
            $validatedData['image_url'] = $newImageName;
        }


        $request->session()->put('validatedData', $validatedData);
        if ($request->session()->has('validatedData')) {
            return response()->json(['status' => 1, 'message' => 'User registered successfully'], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'User registration failed. Please try again. '], 500);
        }
    }



    public function passwordAdd(Request $request)
    {
        $emailInSession = Session::get('email');
        if (empty($emailInSession)) {
            return response()->json(['status' => 0, 'message' => 'User registration not alow'], 500);
        }
        $validatedData = $request->validate([
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $sessionValues = $request->session()->get('validatedData');

        // var_dump($uploadedFile);
        if (!empty($sessionValues)  && !empty($emailInSession)) {

            $user = new User();
            $user->name = $sessionValues['name'];
            $user->email = $emailInSession;
            $user->company_name = $sessionValues['copanyName'];
            $user->user_name = $sessionValues['userName'];
            $user->gst_number = $sessionValues['gstNumber'] ?? null;
            $user->image_url = $sessionValues['image_url'] ?? null;
            $user->address = $sessionValues['address'] ?? null;
            $user->city = $sessionValues['city'] ?? null;
            $user->state = $sessionValues['state'] ?? null;
            $user->zip_code = $sessionValues['zip_code'] ?? null;
            $user->password = bcrypt($request->input('password'));
            $user->save();

            $request->session()->forget('validatedData');
            $request->session()->forget('email');

            return response()->json(['status' => 1, 'message' => 'User registered successfully'], 200);
        }

        return response()->json(['status' => 0, 'message' => 'No  data found'], 500);
    }

    public function userNameSearch(Request $request)
    {

        $validatedData = $request->validate([
                'user_name' => 'required',

            ]);
        $username = $request->input('user_name');
        $user = User::where('user_name', $username)->first();

        if ($user) {
            return response()->json([
                'status' => 0, 'message' => 'Username not available'
            ], 500);
        } else {

            return response()->json(['status' => 1, 'message' => 'Username available'], 200);
        }
    }
    public function login(Request $request)
    {
        $loginField = filter_var($request->input('emailorusername'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        $validationRules = [
            'password' => 'required'
        ];

        if ($loginField === 'email') {
            $validationRules['emailorusername'] = 'required|email';
        } else {
            $validationRules['emailorusername'] = 'required';
        }

        $validatedData = $request->validate($validationRules);

        $credentials = [
            $loginField => $request->input('emailorusername'),
            'password' => $validatedData['password']
        ];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('UserToken');
            $accessToken = $token->accessToken;
            $user->image_url =  url('/') . $user->image_url;

            return response()->json(['status' => 1, 'access_token' => $accessToken, 'user' => $user], 200);
        }


        return response()->json(['status' => 0, 'message' => 'Invalid credentials'], 401);
    }



    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['status' => 1, 'message' => 'Logged out successfully'], 200);
    }
    // public function logout(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user) {

    //         $user->tokens()->each(function ($token) {
    //             $token->delete();
    //         });
    //         $request->session()->invalidate();
    //         Auth::logout();

    //         return response()->json(['status' => 1, 'message' => 'Logged out successfully'], 200);
    //     }
    //     return response()->json(['status' => 0, 'message' => 'User not authenticated'], 401);
    // }


    public function getUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $additionalData = [
                'status' => 1,
                'message' => 'User data retrieved successfully',
                'user' => $user,
            ];


            if (!empty($user->image_url)) {

                $user->image_url = url('/') . $user->image_url;
            }
            return response()->json($additionalData, 200);
        } else {

            return response()->json(['status' => 0, 'message' => 'User not authenticated'], 401);
        }
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validation for the fields in the request
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:50',
            'second_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|numeric:10',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:200',
            'state' => 'nullable|string|max:200',
        ]);
        $city =  $request->city ?? '';
        $state = $request->state ?? '';
        // Retrieve the specific user instance
        $user = User::find($user->id);

        if ($user) {
            // Update the user model instance with validated data
            $user->name = $request->input('name', $user->name);
            $user->second_name = $request->input('second_name', $user->second_name);
            $user->phone = $request->input('phone', $user->phone);
            $user->company_name = $request->input('company_name', $user->company_name);
            $user->gst_number = $request->input('gst_number', $user->gst_number);
            $user->address = $request->input('address', $user->address) . ', ' . $city . ', ' . $state;

            $user->save();
            return response()->json(['status' => 1, 'message' => 'User profile updated successfully'], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }
    }

    public function userProfileImageUpdate(Request $request)
    {
        $user = $request->user();
        $user = User::find($user->id);
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {

            if ($user->image_url) {
                $oldImagePath = public_path($user->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('adminProfile/userImage'), $imageName);

            $user->image_url = '/adminProfile/userImage/' . $imageName;
            $user->save();

            $fullImageUrl = url('/') . $user->image_url;
            return response()->json(['status' => 1, 'message' => 'Image updated successfully', 'image_url' => $fullImageUrl], 200);
        } else {
            if ($user->image_url) {
                $fullImageUrl = url('/') . $user->image_url;
                return response()->json(['status' => 1, 'message' => 'No new image provided', 'image_url' => $fullImageUrl], 200);
            } else {

                return response()->json(['status' => 0, 'message' => 'User does not have an image'], 404);
            }
        }
    }

    // public function updateEmail(Request $request)
    // {

    //     $user = $request->user();
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
    //     }
    //     $email = $request->input('email');


    //     $otp = mt_rand(1000, 9999);
    //     Mail::send([], [], function ($message) use ($request, $otp) {
    //         $message->to($request->email)
    //             ->subject('Your OTP for verification')
    //             ->html($this->emailTemp($otp, 'Update Eamil'));
    //     });


    //     Session::put('update_Eamil_otp', $otp);
    //     Session::put('upadte_email_id', $request->email);

    //     return response()->json(['status' => 1, 'message' => 'OTP sent successfully', 'otp' => $otp]);
    // }


    public function updateEmail(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }

        $email = $request->input('email');
        $userWithEmail = User::where('email', $email)->first();

        if ($userWithEmail) {
            return response()->json(['status' => 0, 'message' => 'Email is already Added'], 422);
        }
        $otp = mt_rand(
            1000,
            9999
        );
        $otpExpiration = now()->addMinutes(5);

        try {
            Mail::send(
                [],
                [],
                function ($message) use (
                    $email,
                    $otp
                ) {
                    $message
                        ->to($email)
                        ->subject('Your OTP for verification')
                        ->html($this->emailTemp(
                            $otp,
                            'Update Email'
                        ));
                }
            );

            $request->session()->put('update_email_otp', $otp);
            $request->session()->put(
                'update_email_otp_expiry',
                $otpExpiration
            );
            $request->session()->put(
                'update_email_id',
                $email
            );

            return response()->json(['status' => 1, 'message' => 'OTP sent successfully', 'otp' => $otp]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Failed to send OTP. Please try again later.']);
        }
    }


    public function verifyUpdateEmailOTP(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer|digits_between:4,4',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }
        $email = $request->session()->get('update_email_id');
        $storedOTP = $request->session()->get('update_email_otp');
        $otpExpiration = $request->session()->get('update_email_otp_expiry');

        if (!$email || !$storedOTP || !$otpExpiration) {
            return response()->json(['status' => 0, 'message' => 'No pending email update request found.'], 404);
        }

        $userInputOTP = $request->input('otp');

        if ($userInputOTP != $storedOTP || now() > $otpExpiration) {
            return response()->json(['status' => 0, 'message' => 'Invalid or expired OTP. Please request a new one.'], 400);
        }

        $user->email = $email;
        $user->save();

        $request->session()->forget(['update_email_otp', 'update_email_otp_expiry', 'update_email_id']);

        return response()->json(['status' => 1, 'message' => 'Email updated successfully', 'Update_email' =>  $email]);
    }

    // public function useraddbyAdmin(Request $request)
    // {

    //     $userlogin =   $request->user();
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'second_name' => 'nullable',
    //         'email' => 'required|email|unique:users',
    //         'phone' => 'nullable|numeric|min:10',
    //         'user_name' => 'required|unique:users',
    //         'password' => 'required|min:6',
    //         'password_confirmation' => 'required|min:6|same:password',
    //         'company_name' => 'required',
    //         'gst_number' => 'nullable',
    //         'address' => 'nullable',
    //         'role' => 'required|in:pos,userAccess',
    //         'city' => 'nullable|string|max:200',
    //         'state' => 'nullable|string|max:200',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
    //     }



    //     try {


    //         $user = new User();
    //         $user->name = $request->input('name');
    //         $user->second_name = $request->input('second_name');
    //         $user->email = $request->input('email');
    //         $user->phone = $request->input('phone');
    //         $user->user_name = $request->input('user_name');
    //         $user->password = bcrypt($request->input('password'));
    //         $user->company_name = $request->input('company_name');
    //         $user->gst_number = $request->input('gst_number');
    //         $user->role = $request->input('role');
    //         $user->user_added_by = $userlogin->id;
    //         $address = $request->input('address');
    //         $city = $request->input('city');
    //         $state = $request->input('state');

    //         if ($city && $state) {
    //             $user->address = $address . ', ' . $city . ', ' . $state;
    //         } else {
    //             $user->address = $address ?? '';
    //         }


    //         if ($request->hasFile('image')) {
    //             $image = $request->file('image');
    //             $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
    //             $image->move(public_path('adminProfile/subAdminImage'), $imageName);

    //             $user->image_url = '/adminProfile/subAdminImage/' . $imageName;
    //         }

    //         $user->save();

    //         return response()->json(['status' => 1, 'message' => 'User created successfully', 'user' => $user], 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 0, 'message' => 'Failed to create user. Please try again later.'], 500);
    //     }
    // }

    public function useraddbyAdmin(Request $request)
    {

        $userlogin = $request->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'second_name' => 'nullable',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|numeric|digits:10',
            'user_name' => 'required|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6|same:password',
            'company_name' => 'required',
            'gst_number' => 'nullable',
            'address' => 'nullable',
            'role' => 'required|in:pos,userAccess',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'zipcode' => 'nullable|numeric|digits:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->second_name = $request->input('second_name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->user_name = $request->input('user_name');
            $user->password = bcrypt($request->input('password'));
            $user->company_name = $request->input('company_name');
            $user->gst_number = $request->input('gst_number');
            $user->role = $request->input('role');
            $user->user_added_by = $userlogin->id;
            $user->address = $request->input('address');
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->zipcode = $request->input('zipcode');
            $user->save();
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('adminProfile/subAdminImage'), $imageName);

                $user->image_url = '/adminProfile/subAdminImage/' . $imageName;
                $user->save();
            }

            $fullImageUrl = url('/') . $user->image_url;

            return response()->json(['status' => 1, 'message' => 'User created successfully', 'user' => $user, 'image_url' => $fullImageUrl], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Failed to create user. Please try again later.'], 500);
        }
    }


    public function updateUser(Request $request, $userId)
    {
        $userlogin = $request->user();
        $user = User::where('id', $userId)->where('user_added_by', $userlogin->id)->first();
        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'second_name' => 'nullable',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'nullable|numeric|digits:10',
            'user_name' => 'required|unique:users,user_name,' . $userId,
            'password' => 'nullable|min:6',
            'password_confirmation' => 'nullable|min:6|same:password',
            'company_name' => 'required',
            'gst_number' => 'nullable',
            'role' => 'required|in:pos,userAccess',
            'address' => 'nullable',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'zipcode' => 'nullable|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }

        try {

            $fieldsToUpdate = [
                'name', 'second_name', 'email', 'phone', 'user_name', 'company_name', 'gst_number', 'role',

            ];

            foreach ($fieldsToUpdate as $field) {
                if ($request->has($field)) {
                    $user->$field = $request->input($field);
                }
            }


            $user->address = $request->input('address');
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->zipcode = $request->input('zipcode');

            if ($request->filled('password')) {
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();


            return response()->json(['status' => 1, 'message' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Failed to update user. Please try again later.'], 500);
        }
    }


    public function deleteUser(Request $request, $userId)
    {
        $userlogin = $request->user();
        $user = User::where('id', $userId)->where('user_added_by', $userlogin->id)->first();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }

        try {
            $user->delete();

            return response()->json(['status' => 1, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Failed to delete user. Please try again later.'], 500);
        }
    }



    public function getAdminUser(Request $request)
    {
        $userlogin = $request->user();

        $validator = Validator::make($request->all(), [
            'role' => 'sometimes|required|in:pos,userAccess',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }

        $users = User::where('user_added_by', $userlogin->id);

        if ($request->has('role')) {
            $role = $request->input('role');
            $users->where('role', $role);
        }

        $users = $users->paginate(10);

        $users->getCollection()->transform(function ($user) {
            if (!empty($user->image_url)) {
                $user->image_url = url('/') . $user->image_url;
            }
            return $user;
        });


        return response()->json(['status' => 1, 'message' => 'Admin users retrieved successfully', 'users' => $users]);
    }
}
