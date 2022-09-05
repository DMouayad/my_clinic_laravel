<?php

namespace App\Traits;


trait UpdatesModelAttributes
{
    /**
     * 
     * 
     * @param  $model
     * @param array $attributes
     * @return mixed
     */
    public function setModelAttributes($model, array $attributes)
    {
        foreach ($attributes as $key => $value) {

            $model->setAttribute($key, $value);
        }
        return $model;
    }
}
