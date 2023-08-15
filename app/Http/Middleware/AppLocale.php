<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppLocale
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     *
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->exists('lang')) {
            $requestLang = $request->only('lang');
            $validationLang = app('validator')->make($requestLang, [
                'lang' => 'alpha|min:2',
            ]);

            if (!$validationLang->fails()) {
                app('translator')->setLocale($request->input('lang'));
            } else {
                throw new ValidationException($validationLang);
            }
        }

        return $next($request);
    }
}
