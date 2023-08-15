<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Admin\LoginRequest;
use App\Locale\BaseLocale;
use App\Models\AdminModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->safe()->only(['login', 'password']);
        if(Auth::guard('admin_guard')->attempt($credentials)){
            $admin = AdminModel::where('login', $credentials['login'])->first();
            $token = $admin->createToken('admin_token')->plainTextToken;
            return ResponseHelper::success([
                'admin' => $admin,
                'token' => $token,
                'locales' => BaseLocale::SUPPORTED_LOCALES_CODES,
            ]);
        }
        return ResponseHelper::errorKey('password', trans('errors.wrong_login'));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $admin = Auth::guard('admin_guard')->user();
        if(!empty($admin)) {
            $tokens = $admin->tokens();
            if(!empty($tokens)) {
                $tokens->where('id', $admin->currentAccessToken()->id)->delete();
            }
        }
        return ResponseHelper::ok();
    }
}
