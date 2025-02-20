<?php
namespace App\Http\Helpers;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Upload
{
    static function image($file, $destinationPath)
    {
        $fileName = time() . '-' . Str::random(6) . '-' . uniqid() . '.' . $file->extension();
        $fileExtension = $file->extension();
        $fileSize = $file->getSize();
        $FileMime = $file->getClientMimeType();
        $parentPath = 'uploads';
        //make path if not exists
        $path = public_path($parentPath.'/'.$destinationPath);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        //resize if files have any images
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read($file);

            // resize image proportionally to 300px width
            $image->scale(width: 1000);

            // save modified image in new format 
            $image->save($path . '/' . $fileName);

        } else {
            if ($file && is_file($file) && is_readable($file)) {
                $file->move($path, $fileName);
            }
        }

        //data for return to controller
        $sFile['name'] = $fileName;
        $sFile['path'] = $parentPath . '/' .$destinationPath . '/' . $fileName;
        $sFile['extension'] = $fileExtension;
        $sFile['size'] = $fileSize;
        $sFile['mime_type'] = $FileMime;
        return $sFile;
    }

    static function file($file, $destinationPath)
    {
        $fileName = time() . '-' . Str::random(6) . '-' . uniqid() . '.' . $file->extension();
        $fileExtension = $file->extension();
        $fileSize = $file->getSize();
        $FileMime = $file->getClientMimeType();
        $parentPath = 'uploads';
        //make path if not exists
        $path = public_path($parentPath.'/'.$destinationPath);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        //resize if files have any images
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read($file);

            // resize image proportionally to 300px width
            $image->scale(width: 1000);

            // save modified image in new format 
            $image->save($path . '/' . $fileName);

        } else {
            if ($file && is_file($file) && is_readable($file)) {
                $file->move($path, $fileName);
            }
        }

        //data for return to controller
        $sFile['name'] = $fileName;
        $sFile['path'] = $parentPath . '/' .$destinationPath . '/' . $fileName;
        $sFile['extension'] = $fileExtension;
        $sFile['size'] = $fileSize;
        $sFile['mime_type'] = $FileMime;
        return $sFile;
    }
}