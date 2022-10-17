<?php

namespace Domain\StaffMembers\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class StaffMembersUserNameSort implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        return $query->orderByRaw(
            "(select name from users where staff_members.user_id = users.id) " .
            ($descending ? "desc" : "is null")
        );
    }
}
