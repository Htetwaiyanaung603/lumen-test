<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'test_models';
    protected $fillable = [
        'name'
    ];
}
