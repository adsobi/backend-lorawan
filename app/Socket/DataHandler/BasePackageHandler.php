<?php

namespace App\Socket\DataHandler;

use App\Traits\ConversionUtility;
use CryptLib\MAC\Implementation\CMAC;

abstract class BasePackageHandler
{
    use ConversionUtility;

    public string $mhdr;
    public string $mType;
    public string $major;
    public string $rfu;
    public string $phyPayload;
    public string $mic;
    public string $payload;

    protected ?CMAC $hasher;
    protected string $calculatedMIC;

    protected function __construct()
    {
        // $this->phyPayload = bin2hex($this->phyPayload);
        $this->hasher = new CMAC();
        // phyPayload => MHDR | MACPayload | MIC
        $this->mhdr = substr($this->phyPayload, 0, 2);
        $this->mic = substr($this->phyPayload, strlen($this->phyPayload) - 8, 8);
        //specific in mhdr MType | RFU | Major
        $this->rfu = 0; // 000

        $this->mType = self::getMType();
        $this->major = self::getMajor();

        $this->payload = substr($this->phyPayload, 2, strlen($this->phyPayload) - 10);
    }

     //for send Fctrl = '1010
    protected function getMType(): int
    {
        return (hexdec($this->mhdr) & 0xff) >> 5;
    }

    private function getRFU(): int
    {
        return (hexdec($this->mhdr) & 0xff) >> 5;
    }

    private function getMajor(): int
    {
        return (hexdec($this->mhdr) & ((1 << 2) - 1));
    }

    public function makePHYPayload(): string
    {
        return $this->mhdr
            . $this->payload
            . $this->calculatedMIC;
    }

    public function makePayloadWithMIC(): string
    {
        return $this->payload
            . $this->calculatedMIC;
    }

    public function calculateMIC(string $appKey): string
    {
        $cmacInput = $this->mhdr . $this->payload;
        $cmac = $this->hasher->generate(hex2bin($cmacInput), hex2bin($appKey));
        return $this->calculatedMIC = substr(bin2hex($cmac), 0, 8);
    }

    public function encrypt(string $data)
    {
        //$iv =
        return openssl_decrypt(base64_encode(pack('H*', $data)), 'aes-128-cbc', hex2bin($this->appKey), 0);
        // dump(pack('H*', $data));
        // return $this->hasher->generate(
        //     pack('H*', $data),
        // hex2bin($this->appKey));
    }

    function decryptAes($msgHex){

		$iv = '010000000001' . $this->devAddr . '01000000' . '0000';
        //dump(strlen($iv));
		//$key = hex2bin($this->appKey);
		$bytes = openssl_decrypt(pack('H*', $msgHex), "AES-128-CBC", pack("H*", $this->appKey), OPENSSL_ZERO_PADDING, pack('H*', $iv));
        //dump('bytes: ', $bytes);
		$plaintext = base64_encode($bytes);
		$decoded_b64msg = base64_decode($plaintext, true);
		return bin2hex($decoded_b64msg);
}

    // protected function encrypt(string $data, string $appKey): string
    // {

    // }
}
