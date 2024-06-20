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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            $this->isAble('store', User::class);
            return new UserResource(User::create($request->mappedAttribute()));
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to create the resource.', 401);
        }
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
    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = User::FindOrFail($user_id);

            $this->isAble('update', $user);

            $user->update($request->mappedAttribute());

            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error('User cannot be found.', 404);
            
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to update the resource.', 401);
        }
    }

    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::FindOrFail($user_id);

            $this->isAble('replace', $user);

            $user->update($request->mappedAttribute());

            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return $this->error('User cannot be found.', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to replace the resource.', 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('delete', $user);

            $user->delete();
            return $this->ok('User deleted successfully.');
        } catch (ModelNotFoundException $exception) {
            return $this->error('User cannot be found.', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to delete the resource.', 401);
        }
    }
}
