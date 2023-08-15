<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostModel extends Model
{
    protected $connection = 'main';
    protected $table = 'posts';

    const IMAGES_GROUP = 'posts';

    protected $casts = [
        'publication_at' => 'datetime:Y-m-d H:i',

        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'deleted_at' => 'datetime:Y-m-d H:i',
    ];

    public $fillable = [
        'locale',
        'text',
    ];
}
