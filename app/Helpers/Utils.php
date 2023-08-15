<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Utils
 *
 * @package App\Helpers
 */
class Utils
{

    /**
     * Convert the array keys to camelCase.
     *
     * @param array $array
     *
     * @return array
     */
    public static function camelArr($array)
    {
        return static::encodeArray($array);
    }

    /**
     * Encode an array
     *
     * @param array $array
     *
     * @return array
     */
    private static function encodeArray($array) {
        $newArray = [];
        foreach ($array as $key => $val) {
            $newArray[Str::camel($key)] = static::encodeJson($val);
        }

        return $newArray;
    }

    /**
     * @param $value
     *
     * @return array
     */
    private static function encodeJson($value)
    {
        if ($value instanceof Arrayable) {
            return static::encodeArrayable($value);
        } else {
            if (is_array($value)) {
                return static::encodeArray($value);
            } else {
                if (is_object($value)) {
                    return static::encodeArray((array) $value);
                } else {
                    return $value;
                }
            }
        }
    }

    /**
     * Encode an arrayable
     *
     * @param $arrayable
     *
     * @return array
     */
    private static function encodeArrayable($arrayable)
    {
        $array = $arrayable->toArray();

        return static::encodeJson($array);
    }

    /**
     * Object to array.
     *
     * @param $obj
     * @param array $arr
     *
     * @return array
     */
    public static function objectToArray($obj, &$arr = [])
    {
        if (!is_object($obj) && !is_array($obj)) {
            $arr = $obj;
            return $arr;
        }

        foreach ($obj as $key => $value) {
            if (!empty($value)) {
                $arr[$key] = array();
                self::objectToArray($value, $arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }

        return $arr;
    }

    /**
     * Get current Timestamp.
     *
     * @param string $format
     *
     * @return string
     */
    public static function currentDateTime($format = 'Y-m-d H:i:s')
    {
        return Carbon::now()->format($format);
    }

    /**
     * Convert null values to empty string in array
     * @param $fields
     * @param $array
     */
    public static function nullToStrInArray($fields, &$array) {
        if(!is_array($fields)) {
            $fields = [$fields];
        }
        foreach($fields as $f) {
            if(array_key_exists($f, $array)) {
                $array[$f] = strval($array[$f]);
            }
        }
    }
}
