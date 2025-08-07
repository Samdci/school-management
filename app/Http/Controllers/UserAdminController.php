<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class UserAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::where('role', 'admin')->where('id', '!=', Auth::id())->get();
        return view('users', compact('users', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
        ]);

        //$studentclass = StudentClasses::where('id', $validated['student_class_id'])->select('class_name')->first();
        // Generate a random password
        $defaultPassword = 123456789;


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phonenumber' => $validated['phonenumber'],
            'gender' => $validated['gender'],
            'password' => bcrypt($defaultPassword),
            'must_change_password' => true,
        ]);

        //DB::table('users') -> insert([
        //    'name' => 'Samuel Munene',
        //    'email' => 'samuel@gmail.com',
        //    'role' => 'admin',
        //    'password' => Hash::make(123456789)
        //]);



        if ($user){
            alert()->success('Admin added successfully!', 'Default password: ' . $defaultPassword);
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:teacher,student,admin',
        ]);

        // Generate a new random password$
        $defaultPassword = 123456789;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phonenumber = $validated['phonenumber'];
        $user->gender = $validated['gender'];
        $user->role = $validated['role'];
        $user->password = bcrypt($defaultPassword);
        $user->save();

        alert()->success('User updated successfully!',);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        alert()->success('SuccessAlert','User deleted successfully!');
        return redirect()->back();
    }

    public function showResetPasswordForm()
    {
        return view('users.resetPasswordForm');
    }


}


