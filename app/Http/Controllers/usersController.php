<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UserRegistration;
use App\User;

class usersController extends Controller
{
    /**
     * User Registration.
     *
     * @param UserRegistration $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function registration(UserRegistration $request, User $user)
    {
        $user->register($request->all());

        return response()->json(['success' => true], 201);
    }
}
