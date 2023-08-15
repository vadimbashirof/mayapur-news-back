<?php

namespace App\Helpers;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class DbHelper
{
    const MAX_INT = 2147483646;

    /**
     * @param string $intervalMod
     * @return Expression
     */
    public static function currTs(string $intervalMod = ''): \Illuminate\Database\Query\Expression
    {
        $sql = 'CURRENT_TIMESTAMP';
        if(!empty($intervalMod)) {
            $sql .= " + INTERVAL '" . $intervalMod . "'";
        }
        return DB::raw($sql);
    }

    /**
     * @param array $arr
     * @return array|string
     */
    public static function arrayToPgArray(array $arr = []): array|string
    {
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);

        return str_replace('[', '{', str_replace(']', '}', str_replace('"', '', $arr)));
    }

    /**
     * @param array $arr
     * @return array|string
     */
    public static function arrayToPgPath(array $arr = []): array|string
    {
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
        return str_replace('[', '(', str_replace(']', ')', str_replace('"', '', $arr)));
    }

    /**
     * @param $s
     * @param $start
     * @param $end
     * @param $bo
     * @param $bc
     * @return array|null
     */
    public static function pgArrayToArray($s, $start = 0, &$end = null, $bo = '{', $bc = '}'): ?array
    {
        if (empty($s) || $bo != $s[0]) return null;
        $return = [];
        $string = false;
        $quote = '';
        $len = strlen($s);
        $v = '';
        for ($i = $start + 1; $i < $len; ++$i) {
            $ch = $s[$i];
            if (!$string && $bc == $ch) {
                if ('' !== $v || !empty($return)) {
                    $return[] = $v;
                }
                $end = $i;
                break;
            } elseif (!$string && $bo == $ch) {
                $v = self::pgArrayToArray($s, $i, $i, $bo, $bc);
            } elseif (!$string && ',' == $ch) {
                $return[] = $v;
                $v = '';
            } elseif (!$string && ('"' == $ch || "'" == $ch)) {
                $string = true;
                $quote = $ch;
            } elseif ($string && $ch == $quote && '\\' == $s[$i - 1]) {
                $v = substr($v, 0, -1).$ch;
            } elseif ($string && $ch == $quote && '\\' != $s[$i - 1]) {
                $string = false;
            } else {
                $v .= $ch;
            }
        }
        foreach ($return as &$r) {
            if (is_numeric($r)) {
                if (ctype_digit($r)) $r = (int) $r;
                else $r = (float) $r;
            }
        }

        return $return;
    }
}
