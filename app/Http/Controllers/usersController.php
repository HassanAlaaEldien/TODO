<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\ChangePassword;
use App\Http\Requests\Users\searchUsers;
use App\Http\Requests\Users\updatePersonalInfo;
use App\Http\Requests\Users\UserRegistration;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $user->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);;

        return response()->json(['success' => true], 201);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userFeed(Request $request)
    {
        $tasks = Auth::user()->feed($request->all());

        return response()->json(['success' => true, 'tasks' => $tasks], 200);
    }


    /**
     * @param searchUsers $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(searchUsers $request)
    {
        $users = User::search($request['query'])->get();

        return response()->json(['success' => true, 'users' => $users], 200);
    }

    /**
     * Changing User Password.
     *
     * @param ChangePassword $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePassword $request)
    {
        if (!Auth::user()->checkUserOldPassword($request['old_password']))
            return response()->json(['success' => false, 'error' => 'Invalid old password.'], 403);

        Auth::user()->changePassword($request['new_password']);

        return response()->json(['success' => true], 200);
    }

    /**
     * Updating User Info + avatar.
     *
     * @param updatePersonalInfo $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePersonalInfo(updatePersonalInfo $request)
    {
        if (Auth::user()->checkEmailAvailbily($request['email']) != 0)
            return response()->json(['error' => ['email' => 'this email already exist.']], 422);

        $path = null;
        if ($request->hasFile('avatar'))
            $path = Storage::putFile('Users/avatars', $request->file('avatar'));

        Auth::user()->updateInfo($request->all(), $path);

        return response()->json(['success' => true], 200);
    }
}
