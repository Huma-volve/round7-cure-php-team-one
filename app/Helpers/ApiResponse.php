<?php 

namespace App\Helpers;

class ApiResponse{

    public static function success($data=null, $message='success', $code=200 ){
        return response()->json([
            "status" => true,
            "message" => $message,
            "data" => $data,
            "errors" =>null,
        ],$code);
    }

    public static function error($data=[], $message='error', $code= 500 ){
        return response()->json([
            "status" => false,
            "message" => $message,
            "data" => null,
            "errors" => $data,
        ] ,$code);
    }

}