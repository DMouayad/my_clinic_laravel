<?php

namespace App\Traits;

use App\Models\CustomError;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ProvidesResourcesJsonResponse
{
    use ProvidesApiJsonResponse;

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
     * @param \Illuminate\Database\Eloquent\Model $resource
     * @param integer $status
     * @param \App\Models\CustomError|null $error
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function resource(
        Model $resource,
        int $status = 200,
        ?CustomError $error = null
    ) {
        $response_data = [
            "status" => $status,
            "error" => $error,
        ];
        if ($this->resource) {
            return (new $this->resource($resource))->additional($response_data);
        }
    }

    public function setCollection($collection)
    {
        return $this->resource_collection = $collection;
    }

    /**
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection|null
     */
    public function getCollection()
    {
        return $this->resource_collection;
    }

    /**
     * Get this ModelCollectionResource with additional info
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param integer $status
     * @param \App\Models\CustomError|null $error
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function collection(
        Collection $collection,
        int $status = 200,
        ?CustomError $error = null
    ) {
        $response_data = [
            "status" => $status,
            "total" => $collection->count(),
            "error" => $error,
        ];
        // if the resource_collection was provided we can use it to create a ResourceCollection
        if (isset($this->resource_collection)) {
            return $this->resource_collection($collection)->additional(
                $response_data
            );
        }
        // if resource_collection wasn't set we use the provided model Resource::collection
        if (isset($this->resource)) {
            return $this->resource
                ::collection($collection)
                ->additional($response_data);
        }
    }
}
