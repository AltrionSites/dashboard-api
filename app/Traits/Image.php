<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Img;

trait Image
{
    public function createImages($file, $folder, $name, $extension)
    {
        $image = Img::make($file->path())->resize(480, 480, function ($const) {
            $const->aspectRatio();
        });
        $path = $folder.$name.'.'.$extension;
        Storage::put($path, $image->stream());
    }

    public function deleteImages($folder, $name)
    {
        $storedImgName = explode('.', $name);
        $storedImgExtension = ($storedImgName[1]);
        $oldImgName = $storedImgName[0];
        Storage::delete($folder.$oldImgName.'.'.$storedImgExtension); 
    }
}
?>