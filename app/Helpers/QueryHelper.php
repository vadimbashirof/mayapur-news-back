<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QueryHelper
{
    public $table;
    protected $tableSlug = 'a';
    protected $tableCopySlug = 'b';
    public $join = '';

    /**
     * CustomModelService constructor.
     * @param $table
     */
    public function __construct($table) {
        $this->table = $table;
    }

    /**
     * @param $string
     */
    function join($string) {
        $this->join = $string;
    }


    /**
     * Множественное обновление строк
     * @param array $settings
     * Массив должен быть многомерным и сосоять из двух массивов
     * в первом массиве поля с настройками для условия where т.е поиска заменяемых строк
     * во втором изменяемые данные с настройками
     *
     * @param $fill
     * заполняемые данные
     * @throws Exception
     */
    public function updateMultiple($settings, $fill) {
        $query = "UPDATE \"{$this->table}\" AS {$this->tableSlug} SET \n";
        $binds = [];

        $setRows = [];
        $updateRow = [];
        $selectFields = [];
        $whereRow = [];

        foreach ($settings[0] as $field => $setting) {
            $setting = self::getSettings($setting);

            $tableSlug = $this->tableSlug;
            $whereString = "{$this->tableCopySlug}.\"{$field}\"";

            if (Arr::has($setting, 'type')) $whereString .= '::'.$setting['type'];
            if (Arr::has($setting, 'join')) $tableSlug = $setting['join'];

            $whereString .=  " = {$tableSlug}.\"{$field}\" ";
            $whereRow[] = $whereString;
            $selectFields[] = "\"$field\"";
        }

        foreach ($settings[1] as $field => $setting) {
            $setting = self::getSettings($setting);

            $setString = "\"{$field}\" = {$this->tableCopySlug}.\"{$field}\"";
            if (Arr::has($setting, 'type')) $setString .= '::'.$setting['type'];
            $setRows[] = $setString;
            $selectFields[] = "\"$field\"";
        }

        foreach ($fill as $key => $itemFill) {
            $row = [];
            $allSettings = array_merge($settings[0], $settings[1]);
            foreach ($allSettings as $field => $setting) {
                $row[] = ":$field"."$key";
                $binds[$field.$key] = $itemFill[$field];
            }
            $row = implode(",", $row);
            $row = "({$row})";
            $updateRow[] = $row;
        }

        $selectFields = implode(",\n", $selectFields);

        $query .= implode(",\n", $setRows);
        $query .= "\n";
        $query .= "FROM (VALUES \n";
        $query .= implode(",\n", $updateRow);
        $query .= ") AS {$this->tableCopySlug}(\n$selectFields\n) ";
        $query .= "\n";
        if ($this->join) $query .= $this->join;
        $query .= "WHERE \n";
        $query .= implode(" AND ", $whereRow);
        $query .= ';';

        try {
            DB::statement($query, $binds);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Кастомный инсерт
     * умеет работать с конфликтами
     * если произошел конфликт по дефолту ничего не будет
     * если передать в $conflictAction = 'UPDATE' то конфликтные строки заменятся новой информацией
     *
     * @param string $conflictAction ('UPDATE' || 'NOTHING')
     * @param string $conflictCol
     * уникальная колонка для отслеживания конфликтов
     * @param array $settings
     * одномерный массив с полями и их настройками
     * @param array $fill
     * заполняемые данные
     * @throws Exception
     */
    public function insertMultiple($settings, $fill, $conflictAction = 'NOTHING', $conflictCol = '') {
        $binds = [];
        $query = "INSERT INTO \"{$this->table}\" AS {$this->tableSlug} (\n";

        $fields = [];
        $insertRows = [];
        $setRows = [];
        foreach ($settings as $field => $setting) {
            $setting = self::getSettings($setting);
            $fields[] = "\"$field\"";
            $setRows[] = "\"{$field}\" = excluded.\"{$field}\"::".$setting['type'];
        }

        foreach ($fill as $key => $itemFill) {
            $insertRow = [];
            foreach ($settings as $field => $setting) {
                $setting = self::getSettings($setting);
                $insertRow[] = ":$field".$key."::".$setting['type'];
                $binds[$field.$key] = $itemFill[$field];
            }
            $insertRow = implode(",", $insertRow);
            $insertRow = "({$insertRow})";
            $insertRows[] = $insertRow;
        }

        $query .= implode(",\n", $fields) . "\n)\n";
        $query .= "VALUES\n" . implode(",\n", $insertRows) . "\n";
        $query .= "ON CONFLICT ";

        if (strtoupper($conflictAction) == 'UPDATE' && $conflictCol) {
            $query .= "(\"{$conflictCol}\") DO UPDATE \n";
            $query .= "SET\n" . implode(",\n", $setRows);
        } else if (strtoupper($conflictAction) == 'NOTHING') {
            $query .= "DO NOTHING";
        }

        try {
            DB::statement($query, $binds);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Возвращаем настройки в удобном виде
     * @param $setting
     * @return array
     */
    public static function getSettings($setting) {
        $result = [];
        $setting = explode('|', $setting);
        foreach ($setting as $item) {
            $item = explode(':', $item);
            $result[$item[0]] = $item[1];
        }
        return $result;
    }
}
