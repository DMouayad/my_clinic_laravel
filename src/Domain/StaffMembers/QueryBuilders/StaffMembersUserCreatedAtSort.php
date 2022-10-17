<?php

namespace Domain\StaffMembers\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class StaffMembersUserCreatedAtSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        return $query->orderByRaw(
            ($descending ? "" : "-") .
                "(select created_at from users where staff_members.user_id = users.id) desc"
        );
    }
}
