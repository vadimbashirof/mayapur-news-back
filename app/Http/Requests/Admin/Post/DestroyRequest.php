<?php

namespace App\Http\Requests\Admin\Post;

use App\Helpers\DbHelper;
use App\Http\Requests\Admin\BaseAdminRequest;

class DestroyRequest extends BaseAdminRequest
{
    protected bool $isSetId = true;

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:posts,id,deleted_at,NULL|min:1|max:'.DbHelper::MAX_INT,
        ];
    }
}
