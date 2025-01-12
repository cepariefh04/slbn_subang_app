<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function indexRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->level = 'Admin';

        $user->save();
        return redirect()->route('login.index');
    }

    public function indexLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->only('email', 'password');

        if (Auth::attempt($data)) {
            $request->session()->regenerate();

            $user = auth()->user();
            $user->last_login_at = Carbon::now()->toDateTimeString();
            $user->update();

            if ($user->level === 'Admin') {
                return redirect()->route('admin.dashboard');
            } else if ($user->level === 'SarPras') {
                return redirect()->route('sarpras.dashboard');
            } else {
                return redirect()->route('TU.dashboard');
            }
        } else {
            return redirect()->back()->with('gagal', 'email atau password Anda salah!');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.index');
    }
}
