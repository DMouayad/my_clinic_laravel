<?php

namespace Domain\StaffMembers\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class StaffMembersRoleNameSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        return $query->orderByRaw(
            "(select slug from roles where staff_members.role_id = roles.id) " .
                ($descending ? "desc" : "asc")
        );
    }
}
