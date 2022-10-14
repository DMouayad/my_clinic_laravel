<?php

namespace App\Traits;

trait ProvidesOrderedQuery
{
    /**
     * @param \Illuminate\Database\Eloquent\Model::query $query
     * @param string|null $sortBy
     * @param array $allowed sorting will be done only by items in this array
     * @param array $ignored
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orderQueryFromUrlParam(
        $query,
        ?string $sortBy,
        array $allowed,
        array $ignored = []
    ) {
        if (!empty($sortBy)) {
            $sortBy = explode(",", $sortBy);

            foreach ($sortBy as $sort_param) {
                $sort_info = explode(" ", $sort_param);
                $attribute = $sort_info[0];

                // check if its allowed to sort by this attribute and its not from
                // the ignored attributes list
                if (
                    in_array($attribute, $allowed) &&
                    !in_array($attribute, $ignored)
                ) {
                    $query->orderBy($sort_info[0], $sort_info[1]);
                }
            }
        }
        return $query;
    }
}
