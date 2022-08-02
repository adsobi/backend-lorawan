<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function ssl()
    {
        // $data = "IEWwOf+w/qkRf13eW2RAxLPmebdddUiZduUSYcCuWX5p";
        $data = "9e010013000036010b260305184f84e8f528e0a910c3545067693c3fb95e3c15";
        // dump(strlen($data));
        // dump(mb_strlen(hex2bin($data)));
        // $data = "4b05fe12366024c3397a4e02000000000000000000000000000000003d03979b";
        $data = "4b05fe12366024c3397a4e02184f84e8f528e0a910c3545067693c3f3d03979b";
        $pass = "7CB49F63AC807CED46D681D539B40F09";
        // dump(strlen($data));
        // dump(mb_strlen(hex2bin($data)));
        // $this->decryption($data,$pass);
        $this->encryption($data, $pass);
        return true;
        // 260B 0136
    }
    private function decryption($data, $pass)
    { //odw
        $d = bin2hex(base64_decode($data));
        $entry = substr($d, 2, 56);
        $x = openssl_encrypt(hex2bin($entry)
            , 'aes-128-ecb', hex2bin($pass));
        // dump($d);
        // dump($a = bin2hex(base64_decode($x)));
        // dd(substr($a, 12, 8));
    }
    private function encryption($data, $pass)
    {
        // dump(hex2bin($data));
        $macPayload = bin2hex(openssl_decrypt(hex2bin($data), 'aes-128-ecb', hex2bin($pass), OPENSSL_RAW_DATA));
    //     dump(openssl_error_string());
    //     dump(strlen($data));
    //     dump($macPayload);
    //     dump($new = "20" . $macPayload . "AE597E69");
    //     dd(base64_encode(hex2bin($new)));
    // }
}
