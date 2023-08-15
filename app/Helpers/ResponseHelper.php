<?php namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ResponseHelper
{
    /**
     * Успешный ответ.
     *
     * @param array $result
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function success(
        array $result = [],
        int $status = ResponseAlias::HTTP_OK
    ): JsonResponse
    {
        return static::handler($result, $status);
    }

    /**
     * Успешный ответ с сообщением.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public static function ok(string $message = 'OK'): JsonResponse
    {
        return static::success(['message' => $message]);
    }

    /**
     * Ответ с ошибкой.
     *
     * @param array $errors
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function error(
        array $errors = [],
        int $status = ResponseAlias::HTTP_BAD_REQUEST
    ): JsonResponse
    {
        return static::handler($errors, $status);
    }


    /**
     * Текстовая ошибка с ключом.
     *
     * @param string $key
     * @param string $message
     * @param int $status
     *
     * @return JsonResponse
     */
    public static function errorKey(
        string $key,
        string $message,
        int $status = ResponseAlias::HTTP_BAD_REQUEST
    ): JsonResponse
    {
        return static::error([
            $key => [$message],
        ], $status);
    }

    /**
     * Общий обработчик ответа.
     *
     * @param array $data
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function handler(array $data, int $status): JsonResponse
    {
        return response()->json(
            Utils::camelArr($data),
            $status,
            [
                'Content-Type' => 'application/json;charset=UTF-8',
                'Charset' => 'utf-8',
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}
