<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $creds = [
            'email' => $request->email,
            'password' => $request->password
        ];
        //Checking User Registeration Code Start
        $user = User::where('email',$request->email)->first();
        if(!$user)
        {
            toastr()->error('User is Not Registered.');
            return redirect()->back();
        }
        //Checking User Registeration Code End
        //User Authentication Code Start
        if(Auth::guard('user')->attempt($creds))
        {
            if($user->role->name == 'Admin')
            {
                toastr()->success('You Login Successfully');
                return redirect()->intended(route('admin.dashboard.index'));
            }
            else{
                Auth::logout();
                toastr()->error('Unauthorized.');
                return redirect()->back();

            }
        } else {
            toastr()->error('Wrong Password.');
            return redirect()->back();
        }
        //User Authentication Code End
    }
    
    public function logout()
    {        
        Auth::logout();
        toastr()->success('You Logout Successfully');
        return redirect('/');
    }
    public function home(Request $request)
    {        
        if($request->roll_number)
        {
            $student = Student::where('roll_number',$request->roll_number)
                        ->whereDate('dob',Carbon::parse($request->dob)->format('Y-m-d'))
                        ->first();
            if($student)
            {
                Download::create([
                    'student_id' => $student->id
                ]);
                return view('front.student.card',compact('student'));
            }else{
                toastr()->error('Not Found.');
                return back();
            }
        }
        return view('front.home.index');
    }
}
