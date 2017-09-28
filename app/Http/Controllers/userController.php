<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UserRegistration;
use App\User;

class userController extends Controller
{
    public function registration(UserRegistration $request, User $user)
    {
        $user->register($request->all());

        return response()->json(['success' => true], 201);
    }
}
