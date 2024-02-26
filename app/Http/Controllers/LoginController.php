<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\TokenRepository;

class loginController extends Controller
{
    public function login(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = User::where('email', $email)->first();
            $response = Http::asForm()->post(env('APP_URL').'oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ]);
            return $response->json();
        } else {
            return response()->json(['status' => 401, 'response' => 'User not found']);
        }
    }

    // public function logout(Request $request, $token)
    // {
    //     $tokenRepository = app(TokenRepository::class);
    //     $tokenRepository->revokeAccessToken($token);
    // }

    public function createUser(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();
        return response($user);
    }

    public function getCurrentUser()
    {
        return response()->json(['user' => Auth::user()]);
    }
}
