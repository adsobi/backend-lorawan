<?php

namespace App\Socket\DataHandler;

use App\Enums\MType;
use App\Enums\Packet;
use App\Models\Downlink;
use App\Socket\DatagramSocket;
use App\Socket\Message\PullDataMessage;
use App\Socket\Message\PushDataMessage;
use App\Socket\Message\TxAckMessage;
class ReceiveDataHandler
{
    protected $fCntUp = 0;
    private function reverseHex(string $value): string
    {
        return join(array_reverse(str_split($value, 2)));
    }
    public function handle(
        DatagramSocket $server,
        string $address,
        string $message
    ) {
        switch (bin2hex(substr($message, 3, 1))) {
            case Packet::PKT_PUSH_DATA->value:
                $messageObj = new PushDataMessage($message);
                $response = hex2bin($messageObj->protocolVersion . $messageObj->token . Packet::PKT_PUSH_ACK->value);
                $server->send($response, $address);

                if (isset($messageObj->payload['rxpk']))
                {

                    $data = base64_decode($messageObj->payload['rxpk'][0]['data']);
                    $phyPayload = bin2hex($data);
                    switch (self::getMType(substr($phyPayload, 0, 2)))
                    {
                        case MType::JOIN_REQUEST->value:

                            $joinRequest = new JoinRequestHandler($phyPayload);
                            $obj = new JoinAcceptHandler($joinRequest->devNonce);
                            dump([
                                'nwkSkey' => $obj->nwkSKey,
                                'appSkey' => $obj->appSKey,
                            ]);
                             Downlink::create([
                                'gateway' => $address,
                                'data' => $obj->createResponse(),
                                'tmst' => $messageObj->payload['rxpk'][0]['tmst'],
                                'freq' => $messageObj->payload['rxpk'][0]['freq'],
                                'modu' => $messageObj->payload['rxpk'][0]['modu'],
                                'datr' => $messageObj->payload['rxpk'][0]['datr'],
                                'codr' => $messageObj->payload['rxpk'][0]['codr'],
                             ]);
                            break;
                        case MType::UNCONFIRMED_DATA_UP->value:
                            $messageObj = new PushDataMessage($message);
                            $preparedData = $this->preparePacketForDecryption($messageObj);
                            $crypter = new loraCrypto("52a1987fc3577ef8f5c6569cef81544f",$preparedData['devAddr']);
                            dump(['decryted'=>$crypter->decrypt($preparedData['data'], $preparedData['fcntUp'])]);
                            break;
                    }
                }
                break;

            case Packet::PKT_PULL_DATA->value:
                $messageObj = new PullDataMessage($message);
                $response = hex2bin($messageObj->protocolVersion . $messageObj->token . Packet::PKT_PULL_ACK->value);
                $server->send($response, $address);
                $downlink = Downlink::all()->first();//TODO multiple gateway handle
                if($downlink){

                    $response2 = json_encode(['txpk'=>[
                        'imme' => false,
                        'tmst' => $downlink->tmst + 5000000,
                        'freq' => $downlink->freq,
                        'rfch' => 0,
                        'powe' => 14,
                        'modu' => $downlink->modu,
                        'datr' => $downlink->datr,
                        'codr' => $downlink->codr,
                        'ipol' => true,
                        'size' => 33,
                        'ncrc'=> true,
                        'data' => $downlink->data
                     ]], JSON_UNESCAPED_SLASHES);

                    $server->send(hex2bin($this->getPullResponseHeader()) . $response2, $address);
                    $downlink->delete();
                }
                break;
        }
        return false;
    }

    public static function getMType(string $mhdr): int
    {
        return (hexdec($mhdr) & 0xff) >> 5;
    }

    public function getPullResponseHeader(): string
    {
        return config('lorawan.protocolVersion') .
            ($this->token = bin2hex(openssl_random_pseudo_bytes(2))) .
            Packet::PKT_PULL_RESP->value;
    }
    public function preparePacketForDecryption($msgObj): array
    {
        $macPayload = substr_replace(substr_replace(bin2hex(base64_decode($msgObj->payload['rxpk'][0]['data'])),"", -8),"",0,2);
        $devAddr = self::reverseHex(substr($macPayload, 0 ,8));
        $foptslen =  hexdec(substr($macPayload, 9 ,1));
        $fcntUp = hexdec(self::reverseHex(substr($macPayload, 10 ,4)));
        $fport = hexdec(self::reverseHex(substr($macPayload,14+($foptslen*2),2)));
        $data = substr($macPayload, -(strlen($macPayload)-16-($foptslen*2)));
        return [
            'devAddr' => $devAddr,
            'fcntUp'=> $fcntUp,
            'fport' => $fport,
            'data' => $data,
        ];
    }
}