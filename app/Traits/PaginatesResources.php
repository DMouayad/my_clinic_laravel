<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait PaginatesResources
{
    /** Per page data count
     * @var integer|null
     */
    private ?int $per_page = null;

    public function setPerPage(int $count)
    {
        $this->per_page = $count;
    }
    /**
     * @param $paginable
     */
    public function paginateWhenNeeded($paginable)
    {

        if (!$this->per_page) {
            $this->per_page = env('PAGINATION_DEFAULT_COUNT');
        }

        if ($paginable->count() > $this->per_page) {
            return $paginable->paginate($this->per_page);
        }

        if (is_a($paginable, Collection::class)) {
            return $paginable;
        } else if (method_exists($paginable, 'get')) {
            return $paginable->get();
        }
    }
}
