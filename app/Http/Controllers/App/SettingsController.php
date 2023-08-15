<?php

namespace App\Http\Controllers\App;

use App\Helpers\ResponseHelper;
use App\Http\Requests\App\Setting\TranslateRequest;
use App\Locale\BaseLocale;
use Illuminate\Support\Facades\Lang;

class SettingsController  extends Controller
{
    public function index()
    {
        return ResponseHelper::success([
            'locales' => BaseLocale::SUPPORTED_LOCALES_WITH_TRANSLATE,
            'defaultLocale' => BaseLocale::EN_LOCALE,
            'limit' => 20,
        ]);
    }

    public function translates(TranslateRequest $request)
    {
        return ResponseHelper::success(Lang::get('app'));
    }
}
