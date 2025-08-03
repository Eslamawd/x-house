<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
 
   public function login(LoginRequest $request)
{
     $request->validated();


    if (User::count() === 0) {
     $user =   User::create([
            'name' => "Admin",
            'email'=> $request->email,
            'role' => "admin",
            'password' => Hash::make($request->password)
        ]);
    } else {

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }
    }
    
     

    auth()->login($user);
    $request->session()->regenerate(); // حماية من جلسات مزورة

    return response()->json(['user' => new UserResource($user)]);
}


public function logout(Request $request)
{
    Auth::guard('web')->logout(); // ← هذا يعمل إذا الحارس هو web
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Logged out successfully']);
}


    public function user(Request $request)
{
    return response()->json(['user' => new UserResource($request->user())]);
}


}
