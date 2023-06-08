<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

if (!function_exists('upLoadImage')) {

    function upLoadImage($imageInRequest)
    {
        $imageName = time() . '.' . $imageInRequest->getClientOriginalExtension();
        Storage::disk('public')->put($imageName, file_get_contents($imageInRequest));

        return $imageName;
    }
}

if (!function_exists('deleteLoadedImage')) {

    function deleteLoadedImage($imageLoaded)
    {

        $isThereAnImage = Storage::disk('public')->exists($imageLoaded);

        if ($isThereAnImage) {
            Storage::disk('public')->delete($imageLoaded);
        }
    }
}

if (!function_exists('updateLoadedImage')) {
    function updateLoadedImage($imageLoaded, $imageToLoad)
    {

        deleteLoadedImage($imageLoaded);
        $imageName = upLoadImage($imageToLoad);

        return $imageName;
    }
}
