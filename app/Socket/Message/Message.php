<?php

namespace App\Socket\Message;

use App\Socket\Parser\Parser;
use stdClass;

class Message
{
    public string $protocolVersion;
    public string $token;
    public string $identifier;
    public ?string $gatewayMAC;
    public ?array $payload;

    public Parser $parser;

    public function __construct(
        public string $data
    ) {
        $this->parser = new Parser($data);
        self::retrieveData($this->data);
    }

    private function retrieveData() : void
    {
        $this->protocolVersion = $this->parser->parse(0, 2);
        $this->token = $this->parser->parse(2, 4);
        $this->identifier = $this->parser->parse(6, 2);
    }
}