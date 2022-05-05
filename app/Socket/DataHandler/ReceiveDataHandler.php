<?php

namespace App\Socket\DataHandler;

use App\Enums\MType;
use App\Enums\Packet;
use App\Socket\DatagramSocket;
use App\Socket\Message\PullDataMessage;
use App\Socket\Message\PushDataMessage;
use App\Socket\Message\TxAckMessage;

class ReceiveDataHandler
{
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
                    //dump(self::getMType(substr($phyPayload, 0, 2)));
                    switch (self::getMType(substr($phyPayload, 0, 2)))
                    {
                        case MType::JOIN_REQUEST->value:
                            dump('JOIN_REQUEST');
                            $joinRequest = new JoinRequestHandler($phyPayload);
                            // dump($value);
                            // $mic = substr($phyPayload, strlen($phyPayload) - 8, 8);
                            // $macPayload = substr($phyPayload, 2, strlen($phyPayload) - strlen($mhdr) - strlen($mic));
                            // $appEUI = substr($macPayload, 0, 16);
                            // $devEUI = join(array_reverse(str_split(substr($macPayload, 16, 16), 2)));
                            // $devNonce = substr($macPayload, 32, 4);

                            /**
                             * There checks is end-device authorized in db and then create JoinAcceptHandler
                            */

                            new JoinAcceptHandler($phyPayload, $joinRequest->devNonce);

                            //TODO: send join accept
                            return true;
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
                break;
            case Packet::PKT_PULL_DATA->value:
                $messageObj = new PullDataMessage($message);
                $response = hex2bin($messageObj->protocolVersion . $messageObj->token . Packet::PKT_PULL_ACK->value);
                $server->send($response, $address);
                break;
            case Packet::PKT_PULL_RESP->value:
                break;
            case Packet::PKT_PULL_ACK->value:
                break;
            case Packet::PKT_TX_ACK->value:
                $messageObj = new TxAckMessage($message);
                break;
        }

        dump($messageObj);
        return false;
    }

    public static function getMType(string $mhdr): int
    {
        return (hexdec($mhdr) & 0xff) >> 5;
    }
}