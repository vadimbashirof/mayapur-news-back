<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageModel extends Model
{
    use SoftDeletes;

    protected $connection = 'main';
    protected $table = 'images';

    protected $fillable = [
        'group_name',
        'group_id',
        'src',
        'order',
    ];
}
