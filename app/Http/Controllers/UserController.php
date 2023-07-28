<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        // TASK: turn this SQL query into Eloquent
        // select * from users
        //   where email_verified_at is not null
        //   order by created_at desc
        //   limit 3

        $users = User::whereNotNull('email_verified_at')
        ->orderBy('created_at','desc')
        ->limit(3)
        ->get();

        return view('users.index', compact('users'));
    }

    public function show($userId)
    {// TASK: find user by $userId or show "404 not found" page
        try {
            $user = User::findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }

        return view('users.show', compact('user'));
    }

    public function check_create($name, $email)
{
     // TASK: find a user by $name and $email
    //   if not found, create a user with $name, $email, and random password

    try {

        $user = User::findOrFail( $name)
                    ->findOrFail($email)
                    ->first();
        }catch(ModelNotFoundException $e){
            $randomPassword = Str::random(12);
            $user = new User([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($randomPassword),
            ]);
            $user->save();

        }

    return view('users.show', compact('user'));
}
    public function check_update($name, $email)
    {
        // TASK: find a user by $name and update it with $email
        //   if not found, create a user with $name, $email and random password
         // updated or created user
        $user = User::where('name', $name)->first();

    if ($user) {
        // If the user is found, update the email
        $user->email = $email;
        $user->save();
    } else {
        // If the user is not found, create a new user
        $randomPassword = Str::random(12);
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($randomPassword),
        ]);
        $user->save();
    }

        return view('users.show', compact('user'));
    }


public function destroy(Request $request)
{
    // TASK: delete multiple users by their IDs
    // SQL: delete from users where id in ($request->users)
    // $request->users is an array of IDs, ex. [1, 2, 3]

    // Insert Eloquent statement here
    User::destroy($request->users);

    return redirect('/')->with('success', 'Users deleted');
}

public function only_active()
{
    $users = User::whereNotNull('email_verified_at')->get();

    return view('users.index', compact('users'));
}


}
