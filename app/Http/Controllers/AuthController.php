<?php

namespace App\Http\Controllers;

use App\Response\Response;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails())
            return Response::invalidField();

        $credentials = $request->only(['username', 'password']);

        if (!Auth::attempt($credentials))
            return Response::error('invalid credentials', 401);

        Auth::user()->update(['token' => md5(Auth::id())]);

        Auth::user()->loginLogs()->create([
            'login_time' => now(),
            'ip_address' => $request->ip(),
        ]);

        return Response::withData('successfully login', Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::user()->update(['token' => null]);

        return Response::success('logout success');
    }

    public function me(Request $request)
    {
        return Response::withData('success', Auth::user());
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required'],
            'new_confirm_password' => ['required'],
        ]);

        if ($validator->fails())
            return Response::invalidField();

        if ($request->new_password !== $request->new_confirm_password)
            return Response::error('password not match', 422);

        $credentials = [
            'username' => Auth::user()->username,
            'password' => $request->old_password
        ];

        if (!Auth::attempt($credentials))
            return Response::error('you enter wrong password', 422);

        Auth::user()->update(['password' => bcrypt($request->new_password)]);

        return Response::success('reset password success');
    }
}
