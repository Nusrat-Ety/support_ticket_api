<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /* For changing wrapper name of collection
    public static $wrap = "Ticket"; */

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'Tickets',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'status' => $this->status,
                'description' => $this->when(
                    $request->routeIs('tickets.show'), $this->description
                ),
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user_id
                    ],
                    'links' => [
                        'self' => route('users.show', $this->user_id)
                    ]
                ],

            ],

            'included' => new UserResource($this->whenLoaded('user')),
            'links' => [
                'self' => route('tickets.show', $this->id)
            ]
        ];
    }
}
