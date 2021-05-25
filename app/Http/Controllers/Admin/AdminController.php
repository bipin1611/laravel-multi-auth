<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Display Login Page
     *
     * @author Bipin Parmar
     *
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Submit post req. for login user.
     *
     * @author Bipin Parmar
     *
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Admin::where('email', $request->email)->exists()) {

            if(\Auth::guard('admin')->attempt($request->only('email','password'),$request->filled('remember'))){
                //Authentication passed...
                return redirect()
                    ->intended(route('admin.dashboard'))
                    ->with('status','You are Logged in as Admin!');
            }

            //Authentication failed...
            return $this->loginFailed();

        }
        return back()->with('failed', 'Please Enter Valid Email ID or Password.');


    }

    /**
     * Logged Out User
     *
     * @author Bipin Parmar
     */
    public function logout(Request $request)
    {
        \Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }


}
