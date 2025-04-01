<?php

declare(strict_types=1);

namespace Util;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

class ImageUtil
{

    public static function upload($imageFile)
    {
        $extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        $imageName = "" . time();
        $fullName = $imageName . '.' . $extension;
        $destination = "./../uploads/" . $fullName;
        try {
            if (!move_uploaded_file($imageFile['tmp_name'], $destination)) throw new \Exception("Failed to move file");
            $url = "/uploads/$fullName";
            return [
                'error' => false,
                'url' => $url,
                'fileName' => $imageName
            ];
        } catch (\Exception $e) {
            $errorMessage = "Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
            error_log($errorMessage);
            return [
                'error' => true,
                'url' => null,
                'fileName' => null
            ];
        }
    }


    public static function delete($url)
    {
        try {
            // $filePath = "C:/Dev/Projects/PHP/gus-app/uploads/1743535448.png";
            $url = "./..$url";

            if (!file_exists($url)) {
                throw new \Exception("File does not exist: $url");
            }

            if (!unlink($url)) {
                throw new \Exception("Failed to delete file: $url");
            }

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
