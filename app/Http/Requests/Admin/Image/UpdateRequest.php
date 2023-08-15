<?php

namespace App\Http\Requests\Admin\Image;

use App\Http\Requests\BaseApiRequest;

class UpdateRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.*.id' => 'required|integer',
            'data.*.order' => 'required|integer',
        ];
    }
}
