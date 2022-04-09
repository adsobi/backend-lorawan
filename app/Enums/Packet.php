<?php

namespace App\Enums;

enum Packet: string {

    case PKT_PUSH_DATA = '00';     // To server
    case PKT_PUSH_ACK = '01';      // To gateway
    case PKT_PULL_DATA = '02';     // To server
    case PKT_PULL_RESP = '03';     // To gateway
    case PKT_PULL_ACK = '04';      // To gateway
    case PKT_TX_ACK = '05';        // To server
}