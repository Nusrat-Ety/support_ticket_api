<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\Api\UserResource;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;

class AuthorController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filter)
    {
       return UserResource::collection(
        User::select('users.*')
        ->join('tickets', 'users.id', '=', 'tickets.user_id')
        ->filter($filter)
        ->distinct()
        ->paginate()
       );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $author)
    {
        if ($this->include('tickets')) {
            return new UserResource($author->load('tickets'));
        }
        return new UserResource($author);
    }
}
