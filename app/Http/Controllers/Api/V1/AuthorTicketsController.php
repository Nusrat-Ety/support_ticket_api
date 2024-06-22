<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\Api\TicketResource;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;

class AuthorTicketsController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    public function index(User $author, TicketFilter $filters)
    {
        // Retrieve tickets associated with the specified author ID & apply any filters provided.

        return TicketResource::collection(Ticket::where('user_id', $author->id)->filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttribute([
                'author' => 'user_id'
            ])));
        }

        return $this->notAuthorized('You are not authorized to create the resource.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $author, Ticket $ticket)
    {
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();
            return $this->ok('Ticket deleted successfully.');
        }
        return $this->notAuthorized('You are not authorized to delete the resource.');
    }

    /**
     * replace the specified resource in storage.
     */
    public function replace(ReplaceTicketRequest $request, User $author, Ticket $ticket)
    {
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttribute());
            return new TicketResource($ticket);
        }
        return $this->notAuthorized('You are not authorized to replace the resource.');
    }


    /**
     * update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, User $author, Ticket $ticket)
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttribute());
            return new TicketResource($ticket);
        }
        return $this->notAuthorized('You are not authorized to update the resource.');
    }
}
