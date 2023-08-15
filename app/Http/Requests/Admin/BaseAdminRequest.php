<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Support\Facades\Auth;

class BaseAdminRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }
}
