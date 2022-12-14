<?php

namespace Support\Traits;

use App\Models\CustomError;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Support\Helpers\CollectionPaginationHelper;

trait ProvidesResourcesJsonResponse
{
    use ProvidesOrderedQuery;

    private ?int $per_page = null;
    /**
     * Name of the Model's Resource class.
     *
     * @var string|null
     */
    private ?string $resource;
    /**
     * Name of the Model's ResourceCollection class.
     *
     * @var string|null
     */
    private ?string $resource_collection;

    /** Set the number of items to be returned in a paginated CollectionResource.
     *
     *  Default to [env.PAGINATION_DEFAULT_COUNT]
     * @param int $count
     * @return void
     */
    public function setPerPageCount(int $count)
    {
        $this->per_page = $count;
    }

    /**
     *
     * @return string|null \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function setResource(string $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get this ModelResource with additional info
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $resource
     * @param integer $status
     * @param \App\Models\CustomError|null $error
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function resource(
        $resource,
        int $status = 200,
        ?CustomError $error = null
    ): ?\Illuminate\Http\Resources\Json\JsonResource {
        $response_data = [
            "status" => $status,
            "error" => $error,
        ];
        if ($this->resource) {
            return (new $this->resource($resource))->additional($response_data);
        }
        return null;
    }

    public function setCollection($collection)
    {
        return $this->resource_collection = $collection;
    }

    public function getCollection()
    {
        return $this->resource_collection;
    }

    /**
     * Returns a paginated CollectionResource of the provided model collection
     *
     * @param mixed $collection
     * @param integer $status
     * @param \App\Models\CustomError|null $error
     * @param int|null $per_page
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function paginatedResourceCollection(
        $collection,
        int $status = 200,
        ?CustomError $error = null,
        ?int $per_page = null
    ): ?JsonResource {
        if (is_a($collection, LengthAwarePaginator::class)) {
            $paginated_collection = $collection;
        } elseif (is_a($collection, Collection::class)) {
            $paginated_collection = CollectionPaginationHelper::paginate(
                $collection,
                $per_page ?? $this->getPerPageCount()
            );
        } else {
            $paginated_collection = $collection->paginate(
                $per_page ?? $this->getPerPageCount()
            );
        }
        return $this->collection($paginated_collection, $status, $error);
    }

    public function getPerPageCount()
    {
        return $this->per_page ?? config("my_clinic.PER_PAGE_DEFAULT_COUNT");
    }

    /**
     * Returns a CollectionResource of the provided model collection
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $collection
     * @param integer $status
     * @param \App\Models\CustomError|null $error
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function collection(
        $collection,
        int $status = 200,
        ?CustomError $error = null
    ) {
        $response_data = [
            "status" => $status,
            "error" => $error,
        ];
        // if the resource_collection was provided we can use it to create a ResourceCollection
        if (isset($this->resource_collection)) {
            return $this->resource_collection($collection)->additional(
                $response_data
            );
        }
        // else we use the provided model Resource::collection
        if (isset($this->resource)) {
            return $this->resource
                ::collection($collection)
                ->additional($response_data);
        }
    }
}
