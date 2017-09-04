<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountDetails;
use App\Models\UserAddress;
use App\User as User;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AccountDetailsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all account details
        $AccountDetails = AccountDetails::all();
        // Get all account address details
        // $UserAddress = UserAddress::all();

        return view('pages/account/index')->with([
            'AccountDetails' => $AccountDetails
            ]);
    }

    /**
     * Show the Account View Page
     *
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $AccountDetails = AccountDetails::where('id', $id)->get();
        $UserAddress = UserAddress::where('user_id', $id)->get();
        return view('pages/account/view')->with([
            'AccountDetails' => $AccountDetails,
            'UserAddress' => $UserAddress
            ]);
    }

     /**
     * Show the Account Edit Page
     *
     * @return \Illuminate\Http\Response
     */
     public function edit($id)
     {

        $AccountDetails = AccountDetails::where('id', $id)->get();
        $UserAddress = UserAddress::where('user_id', $id)->get();
        return view('pages/account/edit')->with([
            'AccountDetails' => $AccountDetails,
            'UserAddress' => $UserAddress
            ]);
    }

    /**
     * Show the Account Edit Page
     *
     * @return \Illuminate\Http\Response
     */
    public function validate_pwUpdate(array $data)
    {

       $messages = [
       'current-password.required' => 'Please enter current password',
       'password.required' => 'Please enter password',
       ];

       $validator = Validator::make($data, [
        'current_password' => 'required',
        'password' => 'required|same:password',
        'password_confirmation' => 'required|same:password',     
        ], $messages);

       return $validator;
   }

     /**
     * Show the Account Edit Page
     *
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
     {
        if(Auth::Check()) {
            $request_data = $request->All();
            $validator = $this->validate_pwUpdate($request_data);
            if($validator->fails()) {
              return response()->json(array('error' => $validator->getMessageBag()->toArray()), 400);
            } else {  
                $current_password = Auth::User()->password;           
                if(Hash::check($request->input('current-password'), $current_password)) {  
                // Get the inputs from the submission 
                    $firstName = $request->input('firstName');
                    $lastName = $request->input('lastName');
                    $email = $request->input('email');
                    $password = Hash::make($request->input('password'));

                    // Find the User to update
                    $updateUser = User::find($id);

                    $updateUser->firstName = $firstName;
                    $updateUser->lastName = $lastName;
                    $updateUser->email = $email;
                    // Do this function if password is 
                    $updateUser->password = $password;

                    // Update the  Uer to database
                    $updateUser->save();

                    if (!$updateUser) {
                        Session::flash('message', 'There was a problem submitting your form! Please try again!');
                        return redirect()->route('accounts.edit');
                    }
                    else {
                        Session::flash('message', 'You\'ve successfully completed your submission!');
                        return redirect()->route('accounts.view');
                    }
                } else {
                    return redirect()->to('/');
                }
            }
        }
    }
}
