<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class LoginRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'login' => 'required|exists:admins,login|max:255',
            'password' => 'required|string|max:255',
        ];
    }
}
