<?php

namespace App\Http\Requests\Admin\Image;

use App\Http\Requests\BaseApiRequest;

class ShowRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'group_name' => 'required|string',
            'group_id' => 'required|integer',
        ];
    }
}
