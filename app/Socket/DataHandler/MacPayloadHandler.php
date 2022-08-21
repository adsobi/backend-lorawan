<?php

namespace App\Socket\DataHandler;

use App\Enums\MType;
use CryptLib\MAC\Implementation\CMAC;

//downlink from server to end device
//uplink from end device to server
class MacPayloadHandler extends BasePackageHandler
{
    protected string $key;

    /**
     * Lengths are expressed in octets (hexadecimal).
     * 1 is equals 4 bits.
     */
    public function __construct(
        public string $phyPayload
    ) {
        parent::__construct();

        //inside payload => FHDR | FPort | FRMPayload
        $this->FCtrl = substr($this->payload, 8, 2);//substr($this->fhdr, 8, 2);
        $this->FCtrl_ADR = parent::bitExtractor($this->FCtrl, 1, 8);
        //for uplink
        $this->FCtrl_ADRACKReq = parent::bitExtractor($this->FCtrl, 1, 7); //if is 1 response frame is close in end device
        $this->FCtrl_ACK = parent::bitExtractor($this->FCtrl, 1, 6);
        $this->FCtrl_RFU = parent::bitExtractor($this->FCtrl, 1, 5);
        $this->FCtrl_FOptsLen = parent::bitExtractor($this->FCtrl, 4, 1); //if 0 then FOpts is absent
        // FHDR => devAddr | FCtrl | FCnt | FOpts
        $this->fhdr = substr($this->payload, 0, 8 + 2 + 4 + hexdec($this->FCtrl_FOptsLen) * 2);
        $this->devAddr = join(array_reverse(str_split(substr($this->fhdr, 0, 8), 2)));
        $this->FCnt = join(array_reverse(str_split(substr($this->fhdr, 10, 4), 2)));
        $this->FOpts = substr($this->fhdr, 14, ($fhdrLen = strlen($this->fhdr)) - 14) ?: null; //inclide MAC commands if FOptsLen is not equals 0

        $this->FPort = $fhdrLen < strlen($this->payload) ? substr($this->payload, $fhdrLen, 2) : null;
        // if FPort is equals 0 NwkSKey must be used for encypiton
        // if FPort is equals 1..255 AppSKey must be used for encypiton

        $this->FRMPayload = $fhdrLen < strlen($this->payload) ? substr($this->payload, $fhdrLen + 2, strlen($this->phyPayload) - $fhdrLen - 2) : null;
        // FRMPayload must be encrypted

        // //for downlink
        // $this->FPending = parent::bitExtractor($this->FCtrl, 4, 1); //if is 1 server ask end node for open receive window
        // $this->RFU = parent::bitExtractor($this->FCtrl, 2, 1);
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