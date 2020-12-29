<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client', 'entry_fields', 'promotion_mechanic'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}