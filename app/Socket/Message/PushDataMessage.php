<?php

namespace App\Socket\Message;

class PushDataMessage extends Message
{
    public function __construct(
        public string $data
    ) {
        parent::__construct($data);
        self::retrieveOtherData();
    }

    private function retrieveOtherData(): void {

        $this->gatewayMAC = $this->parser->parse(4, 8);
        $this->payload = $this->parser->parsePayload(12);
    }
}