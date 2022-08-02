<?php

namespace App\Socket\DataHandler;

use App\Enums\Packet;
use App\Enums\MType;
use CryptLib\MAC\Implementation\CMAC;

//downlink from server to end device
//uplink from end device to server
/**
 * Not encrypted
 */
class JoinAcceptHandler extends BasePackageHandler
{
    protected string $appKey = '7CB49F63AC807CED46D681D539B40F09';
    public string $joinNonce;
    public string $netID;
    public string $devAddr;
    public string $dlSettings;
    public string $rxDelay;
    public string $cfList;

    public string $nwkSKey;
    public string $appSKey;

    /**
     * Lengths are expressed in octets (hexadecimal).
     * 1 is equals 4 bits.
     */
    public function __construct(
        public string $devNonce,
    ) {
        //parent::__construct();
        $this->hasher = new CMAC();
        $this->mhdr = "20";
        $this->joinNonce = self::reverseHex(bin2hex(openssl_random_pseudo_bytes(3)));
        $this->devAddr = self::reverseHex(bin2hex(openssl_random_pseudo_bytes(4)));
        $this->netID = "000001"; ///Experimental network according to docs
        $this->dlSettings = self::binaryToHex('01001110');
        $this->rxDelay = '02';
        $this->cfList = '184f84e85684b85e84886684586e8400';
        // $this->nwkSKey = bin2hex(self::generatorOfSKey('00000001'));
        // $this->appSKey = bin2hex(self::generatorOfSKey('00000010'));
    }


    // private function generatorOfSKey($binaryValue)
    // {
    //     $c = str_pad(
    //         $binaryValue . self::hexToBinary($this->joinNonce) . self::hexToBinary($this->netID) . self::hexToBinary($this->devNonce),
    //         (16 % ($len = (strlen($this->joinNonce) / 2 + strlen($this->netID) / 2 + strlen($this->devNonce) / 2 + 1))) * 8 + $len * 8,
    //         '0',
    //         STR_PAD_RIGHT
    //     );
    //     // dump((16 % ($len = (strlen($this->joinNonce)/2 + strlen($this->netID)/2 + strlen($this->devNonce)/2 + 1))) * 8 + $len * 8);
    //     //     dump($binaryValue, ' ', self::hexToBinary($this->joinNonce), ' ',  self::hexToBinary($this->netID), ' ', self::hexToBinary($this->devNonce));
    //     // dump(strlen($c));
    //     // dump(hex2bin($this->appKey));
    //     return $this->hasher->generate(
    //         pack('H*', base_convert($c, 2, 16)),
    //         hex2bin($this->appKey)
    //     );
    // }

    public function makePayload(): string
    {
        return $this->payload =
            $this->joinNonce
            . $this->netID
            . $this->devAddr
            . $this->dlSettings
            . $this->rxDelay
            . $this->cfList;
    }

    public function createResponse(): string
    {
        self::makePayload();
        self::calcMIC($this->payload);
        $this->payload = self::decrypt($this->payload . $this->calculatedMIC);
        return base64_encode(hex2bin($this->mhdr . $this->payload));
    }

    public function calcMIC($payload): string
    {
        $cmacInput = $this->mhdr . $payload;
        $cmac = $this->hasher->generate(pack('H*',$cmacInput), pack('H*',$this->appKey));
        dump(['cmac_input'=>$cmacInput, 'cmac' => bin2hex($cmac)]);
        return $this->calculatedMIC = substr(bin2hex($cmac), 0, 8);
    }

    public function decrypt(string $data)
    {
        $x = bin2hex(openssl_decrypt(hex2bin($data), 'AES-128-ECB', hex2bin($this->appKey), OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING));
        return $x;
    }
}
