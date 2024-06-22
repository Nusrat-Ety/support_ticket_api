<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\Api\TicketResource;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttribute()));
        }
        return $this->notAuthorized('You are not authorized to create the resource.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttribute());
            return new TicketResource($ticket);
        }
        return $this->notAuthorized('You are not authorized to update the resource.');
    }

    /**
     * replace the specified resource in storage.
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttribute());
            return new TicketResource($ticket);
        }
        return $this->notAuthorized('You are not authorized to replace the resource.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();
            return $this->ok('Ticket deleted successfully.');
        }
        return $this->notAuthorized('You are not authorized to delete the resource.', 401);
    }
}
