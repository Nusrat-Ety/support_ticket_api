<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\Api\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AuthorTicketsController extends Controller
{
    public function index($author_id, TicketFilter $filters)
    {
        // Retrieve tickets associated with the specified author ID & apply any filters provided.
        
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }
}
