<?php

namespace App\Locale;

class BaseLocale
{
    const EN_LOCALE = 'en';
    const RU_LOCALE = 'ru';

    const SUPPORTED_LOCALES_CODES = [
        self::EN_LOCALE,
        self::RU_LOCALE,
    ];

    const SUPPORTED_LOCALES_WITH_TRANSLATE = [
        self::EN_LOCALE => 'English',
        self::RU_LOCALE => 'Русский',
    ];
}
