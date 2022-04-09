<?php

namespace App\Socket\Message;

class PullDataMessage extends Message
{
    public function __construct(
        public string $data
    ) {
        parent::__construct($data);
        self::retrieveOtherData();
    }

    private function retrieveOtherData(): void {

        $this->gatewayMAC = $this->parser->parse(4, 8);
    }
}