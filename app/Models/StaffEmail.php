<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StaffEmail extends Model
{

    function role()
    {
        return $this->belongsTo(Role::class);
    }
}
