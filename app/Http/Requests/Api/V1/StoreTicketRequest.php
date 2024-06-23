<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isTicketRoute = $this->routeIs('tickets.store');

        $user = Auth::user();

        $authorIdAttribute = $isTicketRoute ? 'data.relationships.author.data.id' : 'author';

        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,H,C,X',

        ];

        if ($isTicketRoute) {
            $rules['data'] = 'required|array';
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        $rules[$authorIdAttribute] = $authorRule . '|size:' . $user->id;

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAttribute] = $authorRule;
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }

    public function bodyParameters()
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The tickets title"
            ],
            'data.attributes.description' => [
                'description' => 'The tickets description'
            ],
            'data.attributes.status' => [
                'description' => 'Status of the ticket. Such as Accept (A), Hold (H), Cancel (X), Completed (C).'
            ],
            
            $this->routeIs('tickets.store') ? 'data.relationships.author.data.id' : 'author' => [
                'description' => 'The author assigned to the ticket'
            ]
        ];
        
        return $documentation;
    }
}
