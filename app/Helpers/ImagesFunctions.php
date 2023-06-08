<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('loadImage')) {

    function upLoadImage($imageInRequest)
    {
        $imageName = time() . '.' . $imageInRequest->getClientOriginalExtension();
        Storage::disk('public')->put($imageName, file_get_contents($imageInRequest));

        return $imageName;
    }
}
