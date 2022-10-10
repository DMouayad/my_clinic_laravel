<?php

namespace App\Traits;


use Illuminate\Support\Str;

trait ProvidesOrderedQuery
{
    /**
     * @param \Illuminate\Database\Eloquent\Model::query $query
     * @param string|null $sortBy
     * @return \Illuminate\Database\Eloquent\Model::query
     */
    public function queryWithSort($query, ?string $sortBy)
    {
        if ($sortBy) {
            $sort_attributes =  explode(',', $sortBy);

            foreach ($sort_attributes as $sort) {
                $order_direction = $sort[0] == '-' ? 'desc' : 'asc';
                $sort = Str::remove(['-', '+'], $sort);
                $query->orderBy($sort, $order_direction);
            }
        }
        return $query;
    }
}
