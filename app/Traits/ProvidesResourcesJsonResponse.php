<?php

namespace App\Traits;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;


trait ProvidesResourcesJsonResponse
{
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
    private ?string  $resource_collection;

    /**
     *
     * @return \\Illuminate\Http\Resources\Json\JsonResource|null
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
     * @param array|null $errors
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function resource(Model $resource, int $status = 200, array|null $errors = null)
    {
        $response_data = [
            'status' => $status,
            'errors' => $errors,
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
     * @param array|null $errors
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function collection(Collection $collection, int $status = 200, array|null $errors = null)
    {
        $response_data = [
            'status' => $status,
            'total' => $collection->count(),
            'errors' => $errors,
        ];
        // if the resource_collection was provided we can use it to create a ResourceCollection 
        if (isset($this->resource_collection)) {
            return $this->resource_collection($collection)->additional($response_data);
        }
        // if resource_collection wasn't set we use the provided model Resource::collection
        if (isset($this->resource)) {
            return $this->resource::collection($collection)->additional($response_data);
        }
    }
}
