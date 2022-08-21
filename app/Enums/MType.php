<?php

namespace App\Enums;

enum MType: int
{
    case JOIN_REQUEST = 0;
    case JOIN_ACCEPT = 1;
    case UNCONFIRMED_DATA_UP = 2;
    case UNCONFIRMED_DATA_DOWN = 3;
    case CONFIRMED_DATA_UP = 4;
    case CONFIRMED_DATA_DOWN = 5;
    case RFU = 6;
    case PROPRIETARY = 7;
}
