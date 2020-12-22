<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $user;
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function store(Request $request)
    {
        $userData = $request->only(['name','email','password']);
        $rules = ['name' => 'required|max:255','email' => 'required|unique:users,email'];
        $validate = Validator::make($userData,$rules);
        if($validate->fails()) 
        return response()->json(
            [
                'message' => 'Erro na validação',
                'errors' => $validate->errors()->all()
            ],
            422
        );
        try {
            $newUser = $this->user->create($userData);
            $token = auth('api')->login($newUser); 
            return ['user'=>$newUser, 'access_token' => $token];
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }
}
