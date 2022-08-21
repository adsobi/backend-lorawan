<?php

namespace App\Enums;

enum CommandIdentifierEndDevice: string
{
    case LINK_CHECK_REQ = '02';
    case LINK_ADR_ANS = '03';
    case DUTY_CYCLE_ANS = '04';
    case RX_PARAM_SETUP_ANS = '05';
    case DEV_STATUS_ANS = '06';
    case NEW_CHANNEL_ANS = '07';
    case RX_TIMING_SETUP_ANS = '08';
    case TX_PARAM_SETUP_ANS = '09';
    case DI_CHANNEL_ANS = '0A';
    // PROPRIETARY 0x80 to 0xFF
}
