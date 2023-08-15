<?php

namespace App\Http\Requests\App\Setting;

use App\Http\Requests\App\BaseAppRequest;
use App\Locale\BaseLocale;
use Illuminate\Validation\Rule;

class TranslateRequest  extends BaseAppRequest
{
    protected bool $pagination = false;

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
