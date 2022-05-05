<?php

namespace App\Socket\Message;

class TxAckMessage extends Message
{
    public function __construct(
        public string $data
    ) {
        parent::__construct($data);
        self::retrieveOtherData();
    }

    private function retrieveOtherData(): void {
        $this->gatewayMAC = $this->parser->parse(8, 16);
        $this->payload = $this->parser->parsePayload(12);
    }
}