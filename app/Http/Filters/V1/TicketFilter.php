<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class TicketFilter extends QueryFilter 
{
    // Include related models in the query results
    public function include($value)
    {
        return $this->builder->with($value);
    }

    // Filter results based on status values
    public function status($value) 
    {
        return $this->builder->whereIn('status', explode(',', $value));
    }

    // Filter results based on title similarity
    public function title($value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('title', 'like', $likeStr);
    }

    // Filter results based on creation date
    public function createdAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            // Filter by a range of creation dates
            return $this->builder->whereBetween('created_at', $dates);
        }

        // Filter by a specific creation date
        return $this->builder->whereDate('created_at', $dates);
    }

    // Filter results based on update date
    public function updatedAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            
            // Filter by a range of update dates
            return $this->builder->whereBetween('updated_at', $dates);
        }

        // Filter by a specific update date
        return $this->builder->whereDate('updated_at', $dates);
    }
}
