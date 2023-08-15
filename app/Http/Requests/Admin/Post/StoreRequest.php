<?php

namespace App\Http\Requests\Admin\Post;

use App\Http\Requests\Admin\BaseAdminRequest;
use App\Locale\BaseLocale;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseAdminRequest
{
    public function rules(): array
    {
        return [
            'locale' => [
                'required',
                Rule::in(BaseLocale::SUPPORTED_LOCALES_CODES),
            ],
            'text' => 'nullable|string|required_without:images',
            'images' => 'nullable|array|required_without:text',
            'images.*' => 'nullable|image',
        ];
    }
}
