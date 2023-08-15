<?php

namespace App\Http\Requests\Admin\Post;

use App\Http\Requests\Admin\BaseAdminRequest;

class IndexRequest extends BaseAdminRequest
{
    protected bool $pagination = true;

    public function rules()
    {
        return [];
    }
}
