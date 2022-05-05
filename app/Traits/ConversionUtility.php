<?php

namespace App\Traits;

trait ConversionUtility
{
    /**
     *  Starts from LSB bit
     */
    protected function bitExtractor(string $value, int $numberOfbits, int $position)
    {
        return str_pad(decbin(((1 << $numberOfbits) - 1) &
            (hexdec($value) >> ($position - 1))), $numberOfbits, '0', STR_PAD_LEFT);
    }

    /**
     * Reverts hex chain
     */
    protected function reverseHex(string $value): string
    {
        return join(array_reverse(str_split($value, 2)));
    }

    function hexToBinary(string $value): string
    {
        return str_pad(base_convert($value, 16, 2), strlen($value) * 4, '0', STR_PAD_LEFT);
    }

    function binaryToHex(string $value): string
    {
        return str_pad(base_convert($value, 2, 16), strlen($value) / 4, '0', STR_PAD_LEFT);
    }

}