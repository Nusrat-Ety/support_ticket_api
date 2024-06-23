<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\Api\TicketResource;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\User;

class AuthorTicketsController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Retrieves all tickets created by a specific user.
     * 
     * @urlParam author_id integer required the author's id.
     * 
     * @queryParam sort string data field(s) to sort by. Separate multiple fields with commas. Denotes descending sorts with a minus sign. Example: sort=title, -createdAt
     * 
     * @queryParam include relationships related to tickets. Example: Author
     * 
     * @queryParam filter[column_name] Filter by string data field of the tickets. Example: Filter by name. Wildcards are supported. Example: filter[name]=*val*
     * 
     * @group Managing Tickets by Authors
     * 
     * @response 200 {
     * 
     *  "data": [
                {
                    "type": "Tickets",
                    "id": 106,
                    "attributes": {
                        "title": "Test ticket update",
                        "status": "A",
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
            ],
            "links": {
                "first": "http://127.0.0.1:8000/api/v1/authors/14/tickets?page=1",
                "last": "http://127.0.0.1:8000/api/v1/authors/14/tickets?page=1",
                "prev": null,
                "next": null
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 1,
                "links": [
                    {
                        "url": null,
                        "label": "&laquo; Previous",
                        "active": false
                    },
                    {
                        "url": "http://127.0.0.1:8000/api/v1/authors/14/tickets?page=1",
                        "label": "1",
                        "active": true
                    },
                    {
                        "url": null,
                        "label": "Next &raquo;",
                        "active": false
                    }
                ],
                "path": "http://127.0.0.1:8000/api/v1/authors/14/tickets",
                "per_page": 15,
                "to": 1,
                "total": 1
            }
     * }
     * 
     */
    public function index(User $author, TicketFilter $filters)
    {
        // Retrieve tickets associated with the specified author ID & apply any filters provided.

        return TicketResource::collection(Ticket::where('user_id', $author->id)->filter($filters)->paginate());
    }

    /**
     * Create a ticket
     * 
     * Creates a ticket for the specific user.
     * 
     * @group Managing Tickets by Authors
     * 
     * @urlParam author_id integer required The author's id.
     * 
     * @response 200 {
            * "data": {
                "type": "Tickets",
                "id": 107,
                "attributes": {
                    "title": "Store ticket",
                    "status": "X",
                    "description": "this is storing ticket.",
                    "createdAt": "2024-06-23T16:37:21.000000Z",
                    "updatedAt": "2024-06-23T16:37:21.000000Z"
                },
                "relationships": {
                    "author": {
                        "data": {
                            "type": "user",
                            "id": "14"
                        },
                        "links": {
                            "self": "http://127.0.0.1:8000/api/v1/authors/14"
                        }
                    }
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/tickets/107"
                }
            }
     * }
     * 
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
     * Delete an author's ticket
     * 
     * Deletes an author's ticket.
     * 
     * @group Managing Tickets by Authors
     * 
     * @urlParam author_id integer required The author's id.
     * 
     * @urlParam ticket_id integer required The ticket's id.
     * 
     * @response {
     * 
     * "data": [],
        "message": "Ticket deleted successfully.",
        "status": 200
     * }
     * 
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
     * Replace an author's ticket
     * 
     * Replaces an author's ticket.
     * 
     * @group Managing Tickets by Authors
     * 
     * @urlParam author_id integer required The author's id.
     * 
     * @urlParam ticket_id integer required The ticket's id.
     * 
     * @response {
     * 
     * "data": {
                "type": "Tickets",
                "id": 11,
                "attributes": {
                    "title": "replace again ticket",
                    "status": "X",
                    "description": "this is replace again ticket.",
                    "createdAt": "2024-06-16T15:57:28.000000Z",
                    "updatedAt": "2024-06-23T16:31:38.000000Z"
                },
                "relationships": {
                    "author": {
                        "data": {
                            "type": "user",
                            "id": 10
                        },
                        "links": {
                            "self": "http://127.0.0.1:8000/api/v1/authors/10"
                        }
                    }
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/tickets/11"
                }
            }
     * }
     * 
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
     * Update an author's ticket
     * 
     * Updates an author's ticket.
     * 
     * @group Managing Tickets by Authors
     * 
     * @urlParam author_id integer required The author's id.
     * 
     * @urlParam ticket_id integer required The ticket's id.
     * 
     * @response {
     * 
     * "data": {
                "type": "Tickets",
                "id": 11,
                "attributes": {
                    "title": "update user request",
                    "status": "C",
                    "description": "this is update again ticket.",
                    "createdAt": "2024-06-16T15:57:28.000000Z",
                    "updatedAt": "2024-06-23T16:33:36.000000Z"
                },
                "relationships": {
                    "author": {
                        "data": {
                            "type": "user",
                            "id": 10
                        },
                        "links": {
                            "self": "http://127.0.0.1:8000/api/v1/authors/10"
                        }
                    }
                },
                "links": {
                    "self": "http://127.0.0.1:8000/api/v1/tickets/11"
                }
            }
     * }
     * 
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
