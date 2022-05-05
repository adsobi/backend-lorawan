<?php

// use App\Socket\DataHandler\PackageHandler;

// $data = base64_decode('AAAAAAAAAAAAKVwr48QYYeYBAFvXYrU=');
// // $phyPayload = bin2hex($data);
// // $mhdr = substr($phyPayload, 0, 2);
// // $mic = substr($phyPayload, strlen($phyPayload) - 8, 8);
// // $macPayload = substr($phyPayload, 2, strlen($phyPayload) - strlen($mhdr) - strlen($mic));
// // $appEUI = substr($macPayload, 0, 16);
// // $devEUI = join(array_reverse(str_split(substr($macPayload, 16, 16), 2)));
// // $devNonce = substr($macPayload, 32, 4);
// // //echo ($macPayload . $mic);
// // $msg = openssl_decrypt(base64_encode($macPayload . $mic), 'AES-128-ECB', '7CB49F63AC807CED46D681D539B40F09', OPENSSL_ZERO_PADDING);
// // echo ('cd');
// // echo (gettype($msg));
// $key = '7CB49F63AC807CED46D681D539B40F09';
// // include 'PackageHandler.php';
// // include 'app/Enums/MType.php';
// // use App\Socket\DataHandler\PackageHandler as PH;

// $x = new PackageHandler($data);
// echo(bin2hex($x->calculateMIC($key)));

// $secret = '2b7e151628aed2a6abf7158809cf4f3c';
// $input = '6bc1bee22e409f96e93d7e117393172aae2d8a571e03ac9c9eb76fac45af8e51';
// $output = openssl_encrypt(hex2bin($input),'aes-128-ecb',hex2bin($secret),OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);

// echo bin2hex($output);

// $secret = 'B6B53F4A168A7A88BDF7EA135CE9CFCA';
// $input = '204dd85ae608b87fc4889970b7d2042c9e72959b0057aed6094b16003df12de145';
// $output = openssl_encrypt(hex2bin($input),'aes-128-ecb',hex2bin($secret),OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);

// echo bin2hex($output);