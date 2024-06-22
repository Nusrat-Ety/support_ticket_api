<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Policies\V1\UserPolicy;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\Api\UserResource;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Requests\Api\V1\ReplaceUserRequest;

class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filter)
    {
        return UserResource::collection(User::filter($filter)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if ($this->isAble('store', User::class)) {
            return new UserResource(User::create($request->mappedAttribute()));
        }
        return $this->notAuthorized('You are not authorized to create the resource.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($this->isAble('update', $user)) {
            $user->update($request->mappedAttribute());
            return new UserResource($user);
        }
        return $this->notAuthorized('You are not authorized to update the resource.');
    }

    public function replace(ReplaceUserRequest $request, User $user)
    {
        if ($this->isAble('replace', $user)) {
            $user->update($request->mappedAttribute());
            return new UserResource($user);
        }
        return $this->notAuthorized('You are not authorized to replace the resource.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($this->isAble('delete', $user)) {
            $user->delete();
            return $this->ok('User deleted successfully.');
        }
        return $this->notAuthorized('You are not authorized to delete the resource.');
    }
}