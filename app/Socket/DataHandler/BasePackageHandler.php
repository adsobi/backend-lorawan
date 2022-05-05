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

    protected function makePHYPayload(): string
    {
        return $this->mhdr
            . $this->payload
            . $this->calculatedMIC;
    }

    protected function makePayloadWithMIC(): string
    {
        return $this->payload
            . $this->calculatedMIC;
    }

    protected function calculateMIC(string $appKey): string
    {
        $cmacInput = $this->mhdr . $this->payload;
        $cmac = $this->hasher->generate(hex2bin($cmacInput), hex2bin($appKey));
        return $this->calculatedMIC = substr(bin2hex($cmac), 0, 8);
    }

    // protected function encrypt(string $data, string $appKey): string
    // {

    // }
}
