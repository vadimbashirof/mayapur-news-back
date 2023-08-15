<?php

namespace App\Services;

use App\Helpers\QueryHelper;
use App\Helpers\StorageHelper;
use App\Helpers\Utils;
use App\Models\ImageModel;
use Illuminate\Support\Arr;

class ImageService
{
    const IMAGE_FOLDER = 'images';

    /**
     * @param array $files
     * @param string $groupName
     * @param int $groupId
     * @return array
     */
    public static function saveMultiple(array $files, string $groupName, int $groupId): array
    {
        $data = [];
        $order = self::getLastOrder($groupName, $groupId) + 1;

        foreach ($files as $file) {
            $data[] = [
                'group_name' => $groupName,
                'group_id' => $groupId,
                'src' => StorageHelper::saveMd5($file, self::IMAGE_FOLDER),
                'order' => $order,
            ];
            $order++;
        }

        if ($data) ImageModel::insert($data);

        return  self::getAll($groupName, $groupId);
    }

    /**
     * @param array $files
     * @param string $groupName
     * @param int $groupId
     * @return array
     */
    public static function saveOne($file, string $groupName, int $groupId): array
    {
        $order = self::getLastOrder($groupName, $groupId) + 1;
        $image = new ImageModel;

        $image->fill([
            'group_name' => $groupName,
            'group_id' => $groupId,
            'src' => StorageHelper::saveMd5($file, self::IMAGE_FOLDER),
            'order' => $order,
        ]);
        $image->saveOrFail();
        $image = ImageModel::select([
            'id',
            'src',
            'order',
        ])->where('id', $image['id'])
            ->first()
            ->toArray();
        $image['src'] = StorageHelper::pathStorage($image['src']);

        return  $image;
    }

    /**
     * Обновление картинки\картинок
     * Обновляются только поля не содержащие пути к картинкам
     * например order
     * Принимаем многомерный ассоциативный массив ключом является id записи в значении массив с полями которые необходимо обновить
     * в данном случае order
     *
     * Почему так?
     * потому что изменения по сути нет есть удаление картинок и добавление новых этого достаточно
     * изменение нужны только для полей не связанных с src и srcThumb
     * @param array $data
     * @throws \Exception
     */
    public static function update(array $data): void
    {
        $imageCustomModel = new QueryHelper('images');
        $settings = [
            ['id' => 'type:integer'],
            ['order' => 'type:integer'],
        ];
        $imageCustomModel->updateMultiple($settings, $data);
    }

    /**
     * @param string $groupName
     * @param array $array
     * @param string $identifier
     * @return array
     */
    public static function merge(string $groupName, array $array, string $identifier = 'id'): array
    {
        $result = [];
        $groupIds = [];
        foreach ($array as $item) {
            if(Arr::has($item, $identifier))
                $groupIds[] = $item[$identifier];
        }
        $groupImages = self::getGroup($groupName, $groupIds);

        foreach ($array as $item) {
            $id = $item[$identifier];
            if($groupImages && Arr::has($groupImages, $id))
                $item['images'] = $groupImages[$id];
            else
                $item['images'] = [];
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param string $groupName
     * @param array $array
     * @param string $identifier
     * @return array
     */
    public static function flatMerge(string $groupName, array $array, string $identifier = 'id'): array
    {
        $result = [];
        $groupIds = [];
        foreach ($array as $item) {
            if(Arr::has($item, $identifier))
                $groupIds[] = $item[$identifier];
        }
        $groupImages = self::getSimpleGroup($groupName, $groupIds);

        foreach ($array as $item) {
            $id = $item[$identifier];
            if($groupImages && Arr::has($groupImages, $id))
                $item['images'] = $groupImages[$id];
            else
                $item['images'] = [];
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Получить данные удобные для api
     * @param string $groupName
     * @param int $groupId
     * @return array
     */
    public static function getAll(string $groupName, int $groupId): array
    {
        $images = ImageModel::select([
            'id',
            'src',
            'order',
        ])->where([
            ['group_name', $groupName],
            ['group_id', $groupId],
        ])->orderBy('order', 'asc')->get();

        if (!$images) return [];

        $images = $images->toArray();
        foreach ($images as &$image) {
            $image['src'] = StorageHelper::pathStorage($image['src']);
        }

        return $images;
    }

    /**
     * Получить данные удобные для api
     * @param string $groupName
     * @param int $groupId
     * @return array
     */
    public static function getOne(string $groupName, int $groupId): array
    {
        $images = ImageModel::select([
            'id',
            'src',
            'order',
        ])->where([
            ['group_name', $groupName],
            ['group_id', $groupId],
        ])->orderBy('order', 'asc')->get();

        if (!$images) return [];

        $images = $images->toArray();
        foreach ($images as &$image) {
            $image['src'] = StorageHelper::pathStorage($image['src']);
        }

        return $images;
    }

    /**
     * @param string $groupName
     * @param array $groupIds
     * @return array
     */
    private static function getGroup(string $groupName, array $groupIds): array
    {
        $groupImages = [];
        $images = ImageModel::select([
            'id',
            'group_id',
            'src',
            'order',
        ])->where('group_name', $groupName)
            ->whereIn('group_id', $groupIds)
            ->orderBy('order', 'asc')
            ->get();

        if (!$images) return [];
        $images = $images->toArray();

        foreach ($images as $image) {
            $groupImages[$image['group_id']][] = [
                'id' => $image['id'],
                'src' => StorageHelper::pathStorage($image['src']),
                'order' => $image['order'],
            ];
        }

        return $groupImages;
    }

    /**
     * @param string $groupName
     * @param array $groupIds
     * @return array
     */
    private static function getSimpleGroup(string $groupName, array $groupIds): array
    {
        $groupImages = [];
        $images = ImageModel::select([
            'id',
            'group_id',
            'src',
            'order',
        ])->where('group_name', $groupName)
            ->whereIn('group_id', $groupIds)
            ->orderBy('order', 'asc')
            ->get();

        if (!$images) return [];
        $images = $images->toArray();

        foreach ($images as $image) {
            $groupImages[$image['group_id']][] = StorageHelper::pathStorage($image['src']);
        }

        return $groupImages;
    }


    /**
     * Отдаем последний порядок сортировки
     * @param string $groupName
     * @param int $groupId
     * @return int
     */
    private static function getLastOrder(string $groupName, int $groupId): int
    {
        $lastOrder = 0;
        $lastImages = ImageModel::where([
            ['group_name', $groupName],
            ['group_id', $groupId]
        ])->orderBy('order', 'desc')->first();

        if ($lastImages)
            $lastOrder = $lastImages->order;

        return $lastOrder;
    }
}
