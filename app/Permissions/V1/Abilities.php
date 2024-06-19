<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities
{

    // Ticket-related abilities
    public const CreateTicket = 'ticket:create';
    public const UpdateTicket = 'ticket:update';
    public const ReplaceTicket = 'ticket:replace';
    public const DeleteTicket = 'ticket:delete';

    // Ticket-specific own abilities
    public const CreateOwnTicket = 'ticket:own:create';
    public const UpdateOwnTicket = 'ticket:own:update';
    public const DeleteOwnTicket = 'ticket:own:delete';

    // User-related abilities
    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';

    /**
     * Get the abilities for a given user.
     *
     * @param  User  $user  The user for whom to retrieve abilities
     * @return array  An array of abilities based on user's role
     */
    public static function getAbilities(User $user)
    {
        // Don't assign '*'

        if ($user->is_manager) {

            // Return full set of abilities for managers
            return [
                self::CreateTicket,
                self::UpdateTicket,
                self::ReplaceTicket,
                self::DeleteTicket,
                self::CreateUser,
                self::UpdateUser,
                self::ReplaceUser,
                self::DeleteUser,
            ];

        } else {
            
            // Return limited abilities for regular users
            return [
                self::CreateOwnTicket,
                self::UpdateOwnTicket,
                self::DeleteOwnTicket,
            ];
        }
    }
}
