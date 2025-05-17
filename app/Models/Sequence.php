<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'name';

    protected $fillable = ['name', 'value'];
}
