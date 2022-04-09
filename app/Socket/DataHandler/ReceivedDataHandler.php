<?php

namespace App\Socket\DataHandler;

use App\Enums\Packet;
use App\Socket\DatagramSocket;
use App\Socket\Message\PullDataMessage;
use App\Socket\Message\PushDataMessage;
use App\Socket\Message\TxAckMessage;

class ReceivedDataHandler
{
    public static function handle(
        DatagramSocket $server,
        string $address,
        string $message
    ) {
        switch (bin2hex(substr($message, 3, 1))) {
            case Packet::PKT_PUSH_DATA->value:
                $messageObj = new PushDataMessage($message);
                $response = hex2bin($messageObj->protocolVersion . $messageObj->token . Packet::PKT_PUSH_ACK->value);
                $server->send($response, $address);
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
    }
}