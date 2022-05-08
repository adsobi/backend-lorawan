<?php

namespace App\Socket\DataHandler;

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
        $this->mhdr = '20';
        $this->joinNonce = self::reverseHex(bin2hex(openssl_random_pseudo_bytes(3)));
        $this->devAddr = self::reverseHex(bin2hex(openssl_random_pseudo_bytes(4)));
        $this->netID = self::reverseHex(self::binaryToHex(
            self::bitExtractor(bin2hex(openssl_random_pseudo_bytes(3)), 17, 1)
                . self::bitExtractor($this->devAddr, 7, 26)
        ));
        // dump($x = str_pad(decbin(hexdec(bin2hex(openssl_random_pseudo_bytes(2)))), 17, '0', STR_PAD_LEFT). str_pad(decbin(hexdec(self::bitExtractor($this->devAddr, 7, 32))), 7, '0', STR_PAD_LEFT));
        // dump(strlen($x));
        $this->dlSettings = self::binaryToHex('01001110');
        $this->rxDelay = '02';
        $this->cfList = '0000000000000000';
        $this->nwkSKey = bin2hex(self::generatorOfSKey('00000001'));
        $this->appSKey = bin2hex(self::generatorOfSKey('00000010'));
    }

    private function generatorOfSKey($binaryValue)
    {
        $c = str_pad(
            $binaryValue . self::hexToBinary($this->joinNonce) . self::hexToBinary($this->netID) . self::hexToBinary($this->devNonce),
            (16 % ($len = (strlen($this->joinNonce) / 2 + strlen($this->netID) / 2 + strlen($this->devNonce) / 2 + 1))) * 8 + $len * 8,
            '0',
            STR_PAD_RIGHT
        );
        // dump((16 % ($len = (strlen($this->joinNonce)/2 + strlen($this->netID)/2 + strlen($this->devNonce)/2 + 1))) * 8 + $len * 8);
        //     dump($binaryValue, ' ', self::hexToBinary($this->joinNonce), ' ',  self::hexToBinary($this->netID), ' ', self::hexToBinary($this->devNonce));
        // dump(strlen($c));
        // dump(hex2bin($this->appKey));
        return $this->hasher->generate(
            pack('H*', base_convert($c, 2, 16)),
            hex2bin($this->appKey)
        );
    }

    public function makePayload(): string
    {
        return $this->payload =
            self::reverseHex($this->joinNonce, 2)
            . $this->netID
            . $this->devAddr
            . $this->dlSettings
            . $this->rxDelay
            . $this->cfList;
    }

    public function createResponse(): string
    {
        dump('payload: ' ,self::makePayload());
        dump('mic: ', self::calculateMIC($this->appKey));
        dump('devAddr: ', $this->devAddr);
        dump('phyPayload', self::makePHYPayload());
        $this->payload = self::decryptAes(self::makePayloadWithMIC());
        return $this->mhdr . $this->payload . self::calculateMIC($this->appKey);
    }

    public function calcMIC($payload): string
    {
        $cmacInput = $this->mhdr . $payload;
        $cmac = $this->hasher->generate(hex2bin($cmacInput), hex2bin($this->appKey));
        return $this->calculatedMIC = substr(bin2hex($cmac), 0, 8);
    }

    private function decrypt(string $data)
    {
        openssl_encrypt($data, 'aes-128-ecb', $this->appKey, OPENSSL_ZERO_PADDING);
    }
}
