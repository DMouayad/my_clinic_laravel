<?php

namespace App\Traits;

use App\Models\CustomError;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * Returns a paginated CollectionResource modified according to request's query
     *
     * if [request] contains sort parameters it will be added to [model_query]
     * if [request] contains page parameter, the returned collection is paginated by
     * the amount provided using [setPerPageCount], default is [env("PAGINATION_DEFAULT_COUNT")].
     *
     * if[request] doesn't contain a page parameter, all items will be returned in the CollectionResource
     * @param Request $request
     * @param Builder $model_query
     * @param array $relations
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function collectionOfRequestQuery(
        Request $request,
        \Illuminate\Database\Eloquent\Builder $model_query,
        array $relations = []
    ) {
        return $this->paginatedCollection(
            $this->queryWithSort($model_query, $request->sort)->with(
                $relations
            ),
            per_page: $request->page ? null : 10000
        );
    }

    /**
     * Returns a paginated CollectionResource of the provided model collection
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $collection
     * @param integer $status
     * @param \App\Models\CustomError|null $error
     * @param integer|null $per_page
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function paginatedCollection(
        $collection,
        int $status = 200,
        ?CustomError $error = null,
        ?int $per_page = null
    ) {
        if (is_a($collection, LengthAwarePaginator::class)) {
            $paginated_collection = $collection;
        } else {
            $paginated_collection = $collection->paginate(
                $per_page ?? $this->getPerPageCount()
            );
        }
        return $this->collection($paginated_collection, $status, $error);
    }

    public function getPerPageCount()
    {
        return $this->per_page ?? env("PAGINATION_DEFAULT_COUNT");
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
