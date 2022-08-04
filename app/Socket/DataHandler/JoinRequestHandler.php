<?php

namespace App\Socket\DataHandler;

use App\Enums\MType;
use CryptLib\MAC\Implementation\CMAC;

//downlink from server to end device
//uplink from end device to server
/**
 * Not encrypted
 */
class JoinRequestHandler extends BasePackageHandler
{
    protected string $key;
    public string $joinEUI;
    public string $devEUI;
    public string $devNonce;

    /**
     * Lengths are expressed in octets (hexadecimal).
     * 1 is equals 4 bits.
     */
    public function __construct(
        public string $phyPayload
    ) {
        parent::__construct();
        dump($this->payload);
        $this->joinEUI = self::reverseHex(substr($this->payload, 0, 16), 2);
        $this->devEUI = self::reverseHex(substr($this->payload, 16, 16), 2);
        $this->devNonce = substr($this->payload, 32, 4);
        //devNonce must be unique in communication from end device to server on each message

    }

    public function isDataMessage(): bool {
        return ($this->mType >= MType::UNCONFIRMED_DATA_UP && $this->mType <= MType::CONFIRMED_DATA_DOWN)
            ? true
            : false;
    }

    public function response()
    { }

    private function decrypt() {
        openssl_decrypt($this->cMac, 'aes-128-ecb', $this->key, OPENSSL_ZERO_PADDING);
        return substr($this->cMac, 0, 4);

    }
}