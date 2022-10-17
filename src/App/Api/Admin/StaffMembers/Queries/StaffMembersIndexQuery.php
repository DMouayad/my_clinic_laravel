<?php

namespace App\Api\Admin\StaffMembers\Queries;

use Domain\StaffMembers\Models\StaffMember;
use Domain\StaffMembers\QueryBuilders\StaffMembersRoleNameSort;
use Domain\StaffMembers\QueryBuilders\StaffMembersUserCreatedAtSort;
use Domain\StaffMembers\QueryBuilders\StaffMembersUserNameSort;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class StaffMembersIndexQuery extends QueryBuilder
{
    public function __construct(Request $request, array $relations = [])
    {
        $query = StaffMember::query()->with($relations);
        parent::__construct($query, $request);

        $this->allowedFilters([
            AllowedFilter::partial("email"),
            AllowedFilter::partial("username", "user.name"),
        ])->allowedSorts([
            "created_at",
            "email",
            AllowedSort::custom("username", new StaffMembersUserNameSort()),
            AllowedSort::custom("role", new StaffMembersRoleNameSort()),
            AllowedSort::custom(
                "registered_with_at",
                new StaffMembersUserCreatedAtSort()
            ),
        ]);
    }
}
