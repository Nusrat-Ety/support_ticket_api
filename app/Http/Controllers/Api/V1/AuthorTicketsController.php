<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\Api\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketsController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    public function index($author_id, TicketFilter $filters)
    {
        // Retrieve tickets associated with the specified author ID & apply any filters provided.

        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttribute([
                'author' => 'user_id'
            ])));
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to create the resource.', 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('user_id', $author_id)->findOrFail($ticket_id);

            $this->isAble('delete', $ticket);

            $ticket->delete();
            return $this->ok('Ticket deleted successfully.');
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to delete the resource.', 401);
        }
    }

    /**
     * replace the specified resource in storage.
     */
    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('user_id', $author_id)->FindOrFail($ticket_id);
            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttribute());
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to replace the resource.', 401);
        }
    }


    /**
     * update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('user_id', $author_id)->FindOrFail($ticket_id);
            $this->isAble('update', $ticket);
            $ticket->update($request->mappedAttribute());
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to update the resource.', 401);
        }
    }
}
