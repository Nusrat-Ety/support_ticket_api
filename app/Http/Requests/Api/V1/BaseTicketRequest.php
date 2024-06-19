<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{

    public function mappedAttribute()
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.relationships.author.data.id' => 'user_id',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at'
        ];

        $attributesToUpdate = [];

        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }
        
        return $attributesToUpdate;
    }

    public function messages(): array
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid, please use A,C,H or X'
        ];
    }
}
