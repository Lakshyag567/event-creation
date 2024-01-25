<?php

namespace App\Helper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHandler
{

    public static function upload($file, $dirName): bool|string
    {
        if ($file && $dirName) {
            $filName = Str::random() . '.' . $file->getClientOriginalExtension();
            $subFolder = date('FY');
            $destinationPath = Storage::path('public/' . $dirName . '/' . $subFolder);

            //check whether directory exists or not
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            if ($file->move($destinationPath, $filName)) {
                //File Moved :)
                return ($dirName . '/' . $subFolder . '/' . $filName);
            } else {
                //some error occurred
                return false;
            }
        }
        return false;
    }

    public static function delete($filePath): bool
    {
        $oldFilePath = Storage::path('public/' . $filePath);
        //check whether file exists at their path
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
            return true;
        }
        return false;
    }
}