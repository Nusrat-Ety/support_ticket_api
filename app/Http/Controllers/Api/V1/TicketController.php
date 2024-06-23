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

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Get All Tickets
     * 
     * @queryParam sort string data field(s) to sort by. Separate multiple fields with commas. Denotes descending sorts with a minus sign. Example: sort=title, -createdAt
     * 
     * @queryParam include relationships related to tickets. Example: Author
     * 
     * @queryParam filter[column_name] Filter by string data field of the tickets. Example: filter[status]=A,C,H,X. 
     * Filter by title. Wildcards are supported. Example: filter[title]=*val*
     * 
     * @group Managing Tickets
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a Ticket
     * 
     * Creates a new ticket. Users can only create tickets for themselves. Managers can create tickets for any user.
     * 
     * @response 200 {
     * "data": {
                "type": "Tickets",
                "id": 1,
                "attributes": {
                    "title": "test ticket",
                    "status": "A",
                    "description": "test ticket description",
                    "createdAt": "2024-06-23T16:12:12.000000Z",
                    "updatedAt": "2024-06-23T16:12:12.000000Z"
                },
                "relationships": {
                    "author": {
                        "data": {
                            "type": "user",
                            "id": 14
                        },
                        "links": {
                            "self": "http://127.0.0.1:8000/api/v1/authors/14"
                        }
                    }
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/tickets/106"
                }
            }
     * }
     * 
     * @group Managing Tickets
     * 
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttribute()));
        }
        return $this->notAuthorized('You are not authorized to create the resource.');
    }

    /**
     * Show a ticket
     * 
     * Displays an individual ticket to user according to provided id.
     * 
     * @group Managing Tickets
     * 
     */
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }
        return new TicketResource($ticket);
    }

    /**
     * Update a ticket
     * 
     * Update a ticket. User can update their own tickets. Managers can update any tickets.
     * 
     * @group Managing Tickets
     * 
     * @response 200 {
     * 
     *  "data": {
                "type": "Tickets",
                "id": 106,
                "attributes": {
                    "title": "Test ticket update",
                    "status": "A",
                    "description": "test ticket description",
                    "createdAt": "2024-06-23T16:12:12.000000Z",
                    "updatedAt": "2024-06-23T16:13:51.000000Z"
                },
                "relationships": {
                    "author": {
                        "data": {
                            "type": "user",
                            "id": 14
                        },
                        "links": {
                            "self": "http://127.0.0.1:8000/api/v1/authors/14"
                        }
                    }
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/tickets/106"
                }
            }
     * }
     * 
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
     * Replace a ticket
     * 
     * Replace a ticket. User can replace their own tickets. Managers can replace any tickets.
     * 
     * @group Managing Tickets
     * 
     * @response 200 {
     * 
     * "data": {
                "type": "Tickets",
                "id": 106,
                "attributes": {
                    "title": "Test ticket replace",
                    "status": "C",
                    "description": "test ticket description",
                    "createdAt": "2024-06-23T16:12:12.000000Z",
                    "updatedAt": "2024-06-23T16:13:51.000000Z"
                },
                "relationships": {
                    "author": {
                        "data": {
                            "type": "user",
                            "id": 14
                        },
                        "links": {
                            "self": "http://127.0.0.1:8000/api/v1/authors/14"
                        }
                    }
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/tickets/106"
                }
            }
     * }
     * 
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
     * Delete a ticket
     * 
     * Delete a ticket. User can delete their own tickets. Managers can delete any tickets.
     * 
     * @group Managing Tickets
     * 
     * @response 200 
     * {
        "data": [],
        "message": "Ticket deleted successfully.",
        "status": 200
    * }
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
