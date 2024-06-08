<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Ticket extends Model
{
    use HasFactory;

    public function author():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope function for filtering
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);

    }
}
