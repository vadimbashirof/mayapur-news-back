<?php

namespace App\Http\Requests\App\Post;

use App\Http\Requests\App\BaseAppRequest;
use App\Locale\BaseLocale;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseAppRequest
{
    protected bool $pagination = true;

    public function rules()
    {
        return [
            'lang' => [
                'required',
                Rule::in(BaseLocale::SUPPORTED_LOCALES_CODES),
            ],
        ];
    }
}
