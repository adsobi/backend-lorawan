<?php

namespace App\Socket\Parser;

use stdClass;

final class Parser
{
    private ?string $hexData;

    public function __construct(
        private ?string $data,
    ) {
        $this->hexData = bin2hex($this->data);
    }

    public function parse(int $from, int $length): string
    {
        return substr($this->hexData, $from, $length);
    }

    public function parsePayload(int $from): stdClass|null
    {
        return json_decode(substr($this->data, $from, strlen($this->data))) ?? null;
    }
}
