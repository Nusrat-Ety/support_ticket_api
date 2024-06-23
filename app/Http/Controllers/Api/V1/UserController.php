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
     * Get All Users
     * 
     * Only managers can access all of the user data
     * 
     * @queryParam sort string data field(s) to sort by. Separate multiple fields with commas. Denotes descending sorts with a minus sign. Example: sort=name, -createdAt
     * 
     * @queryParam include relationships related to users. Example: tickets
     * 
     * @queryParam filter[column_name] Filter by string data field of the user. Example: Filter by name. Wildcards are supported. Example: filter[name]=*test*
     * 
     * @group Managing User
     */
    public function index(AuthorFilter $filter)
    {
        return UserResource::collection(User::filter($filter)->paginate());
    }

    /**
     * Create a user
     * 
     * Only managers can create a user
     * 
     * @response 200 {
     * "data": {
            "type": "User",
            "id": 14,
            "attributes": {
                "name": "test",
                "email": "test@gmail.com",
                "isManager": false
            },
            "links": {
                "self": "http://127.0.0.1:8000/api/v1/authors/14"
            }
     *}
     * 
     * @group Managing User
     */
    public function store(StoreUserRequest $request)
    {
        if ($this->isAble('store', User::class)) {
            return new UserResource(User::create($request->mappedAttribute()));
        }
        return $this->notAuthorized('You are not authorized to create the resource.');
    }

    /**
     * Show a user
     * 
     * Displays an individual user according to provided id.
     * 
     * @group Managing User
     * 
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }
        return new UserResource($user);
    }

    /**
     * Update a user
     * 
     * Updates an individual user according to provided id. Only managers can update an user information.
     * 
     * @response 200 {
     * "data": {
                "type": "User",
                "id": 14,
                "attributes": {
                    "name": "test 2",
                    "email": "test@email.com",
                    "isManager": false
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/authors/14"
                }
            }
     * }
     * 
     * @group Managing User
     * 
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($this->isAble('update', $user)) {
            $user->update($request->mappedAttribute());
            return new UserResource($user);
        }
        return $this->notAuthorized('You are not authorized to update the resource.');
    }

    /**
     * Replace a user
     * 
     * Replace an individual user according to provided id. Only managers can replace an user information.
     * 
     * @response 200 {
     * 
     * "data": {
                "type": "User",
                "id": 14,
                "attributes": {
                    "name": "test 3",
                    "email": "test@yahoo.com",
                    "isManager": true
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/authors/14"
                }
            }
     * }
     * 
     * @group Managing User
     * 
     */
    public function replace(ReplaceUserRequest $request, User $user)
    {
        if ($this->isAble('replace', $user)) {
            $user->update($request->mappedAttribute());
            return new UserResource($user);
        }
        return $this->notAuthorized('You are not authorized to replace the resource.');
    }

    /**
     * Remove a user
     * 
     * Removes an individual user according to provided id. Only managers can remove an user.
     * 
     * @group Managing User
     * 
     * @response 200 {
     * "data": [],
            "message": "User deleted successfully.",
            "status": 200
     * }
     * 
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
