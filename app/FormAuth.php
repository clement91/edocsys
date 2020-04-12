<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormAuth extends Model
{
    //
    protected $table = 'formauths';

    public function posts()
    {
        return $this->belongsToMany('App\Form');
    }
}
