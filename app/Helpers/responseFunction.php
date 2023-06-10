<?php

if (!function_exists('successResponse')) {

    function successResponse(object $data, string $message)
    {
        return ["success" => true, "data" => $data, "message" => $message];
    }
}

if (!function_exists('errorResponse')) {

    function errorResponse(string $errorMsg)
    {
        return ["success" => false, "error" => $errorMsg];
    }
}
