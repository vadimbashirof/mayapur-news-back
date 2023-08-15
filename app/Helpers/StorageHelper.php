<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Сохраням файл в storage
     * @param $file
     * @param string $folder
     * @return string
     */
    public static function saveMd5($file, string $folder): string
    {
        $fullName = $file->getClientOriginalName();
        $fileName = self::getFileName($fullName);
        $fileName = md5(pathinfo($fileName, PATHINFO_FILENAME) . time());
        $extension = self::getFileExtension($fullName);
        $fileName = $fileName.'.'.$extension;
        $folderName = substr($fileName, 0, 2);

        return $file->storeAs(
            $folder.'/'.$folderName, $fileName, 'public'
        );
    }

    /**
     * @param string $filename
     * @return string
     */
    protected static function getFileExtension(string $filename): string
    {
        $fileInfo = pathinfo($filename);
        return $fileInfo['extension'];
    }

    /**
     * @param string $filename
     * @return string
     */
    protected static function getFileName(string $filename): string
    {
        $fileInfo = pathinfo($filename);
        $extension = $fileInfo['extension'];
        return basename($filename, '.'.$extension);
    }

    /**
     * Gen full url
     * @param $url
     * @return string
     */
    public static function pathStorage($url): string
    {
        return Storage::disk('public')->exists($url) ? Storage::url($url) : '';
    }
}
