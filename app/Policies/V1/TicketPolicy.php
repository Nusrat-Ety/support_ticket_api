<?php

namespace App\Policies\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Permissions\V1\Abilities;

class TicketPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if the user can store (create) a ticket.
     *
     * @param  User  $user  The authenticated user
     * @return bool  Whether the user can store a ticket
     */

    public function store(User $user)
    {
        if ($user->tokenCan(Abilities::CreateTicket)) {
            return true;
        }
        return false;
    }

    /**
     * Check if the user can replace a ticket.
     *
     * @param  User  $user  The authenticated user
     * @return bool  Whether the user can replace a ticket
     */

    public function replace(User $user)
    {
        if ($user->tokenCan(Abilities::ReplaceTicket)) {
            return true;
        }
        return false;
    }

    /**
     * Check if the user can update a ticket.
     *
     * @param  User    $user    The authenticated user
     * @param  Ticket  $ticket  The ticket to be updated
     * @return bool  Whether the user can update the ticket
     */

    public function update(User $user, Ticket $ticket)
    {
        if ($user->tokenCan(Abilities::UpdateTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        }
        return false;
    }

    /**
     * Check if the user can delete a ticket.
     *
     * @param  User    $user    The authenticated user
     * @param  Ticket  $ticket  The ticket to be deleted
     * @return bool  Whether the user can delete the ticket
     */
    public function delete(User $user, Ticket $ticket)
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }
        return false;
    }
}
