<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserResource;
use App\Http\Resources\v1\UserCollection;
use App\Models\User;
use App\Services\v1\UserQuery;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = new UserQuery();
        $queryItems = $filters->transform($request); // [['column', 'operator', 'value']], 
        
        if(count($queryItems) === 0) {
            return new UserCollection(User::all());
        } else {
            return new UserCollection(User::where($queryItems)->get());
        }

    }

    public function show(User $user)
    {
        return new UserResource($user);
    }
}
