<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\Api\TicketResource;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Filters\V1\TicketFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
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
        try {
            
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));

        } catch (ModelNotFoundException $exception) {
            return $this->ok('User not found.', [
                'error' => 'The provided user id does not exists'
            ]);
        }

        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $request->input('data.relationships.author.data.id'),
        ];

        return new TicketResource(Ticket::create($model));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();
            return $this->ok('Ticket deleted successfully.');
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.', 404);
        }
        
    }
}
