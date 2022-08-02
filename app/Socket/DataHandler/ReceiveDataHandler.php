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
                //dd(['responseHEx' => bin2hex($response), 'responseBin' => $response]);
                $server->send($response, $address);

                if (isset($messageObj->payload['rxpk']))
                {

                    $data = base64_decode($messageObj->payload['rxpk'][0]['data']);
                    $phyPayload = bin2hex($data);
                    //dump(self::getMType(substr($phyPayload, 0,    2)));
                    switch (self::getMType(substr($phyPayload, 0, 2)))
                    {
                        case MType::JOIN_REQUEST->value:
                            dump('JOIN_REQUEST');
                            dump($address);
                            $joinRequest = new JoinRequestHandler($phyPayload);
                            /**
                             * There checks is end-device authorized in db and then create JoinAcceptHandler
                            */

                            $obj = new JoinAcceptHandler($phyPayload, $joinRequest->devNonce);

                             Downlink::create([
                                'gateway' => $address,
                                'data' => $obj->createResponse(),
                                'tmst' => $messageObj->payload['rxpk'][0]['tmst'],
                                'freq' => $messageObj->payload['rxpk'][0]['freq'],
                                'modu' => $messageObj->payload['rxpk'][0]['modu'],
                                'datr' => $messageObj->payload['rxpk'][0]['datr'],
                                'codr' => $messageObj->payload['rxpk'][0]['codr'],
                             ]);

                            $this->fCntUp = $this->fCntUp + 1;
                            break;
                        // case MType::JOIN_ACCEPT->value:
                        //     dump('JOIN_ACCEPT');
                        //     break;
                        case MType::UNCONFIRMED_DATA_UP->value:
                            dump('UNCONFIRMED_DATA_UP');
                            break;
                        // case MType::UNCONFIRMED_DATA_DOWN->value:
                        //     dump('UNCONFIRMED_DATA_DOWN');
                        //     break;
                        case MType::CONFIRMED_DATA_UP->value:
                            dump('CONFIRMED_DATA_UP');
                            break;
                        // case MType::CONFIRMED_DATA_DOWN->value:
                        //     dump('CONFIRMED_DATA_DOWN');
                        //     break;
                        case MType::RFU->value:
                            dump('RFU');
                            break;
                        case MType::PROPRIETARY->value:
                            dump('PROPRIETARY');
                            break;
                    }
                }

                break;
            case Packet::PKT_PUSH_ACK->value:
                dump('PKT_PUSH_ACK');
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
            case Packet::PKT_PULL_RESP->value:
                break;
            case Packet::PKT_PULL_ACK->value:
                dump('PKT_PULL_ACK');
                break;
            case Packet::PKT_TX_ACK->value:
                $messageObj = new TxAckMessage($message);
                dump('txack');
                break;
        }

       // dump($messageObj);
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
}