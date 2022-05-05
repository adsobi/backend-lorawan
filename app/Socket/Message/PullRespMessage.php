<?php

namespace App\Socket\Message;

use App\Enums\Packet;
use ReflectionClass;
use ReflectionProperty;

class PullRespMessage
{
    public ?bool $imme;
    public ?int $tmst;
    public ?int $time;
    public ?float $freq;
    public ?int $rfch;
    public ?int $powe;
    public ?string $modu;
    public ?string $datr;
    public ?string $codr;
    public ?int $fdev;
    public ?bool $ipol;
    public ?int $prea;
    public ?int $size;
    public ?string $data;
    public ?bool $ncrc;

    private ?string $token;
    private ?array $accessibleProperties;

    public function __construct()
    {
        $this->accessibleProperties = array_map(
            fn ($value) => $value->name,
            (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC)
        );
    }

    public function setAttributes(array $attributes): void
    {
        array_walk(
            $attributes,
            fn ($value, $key) => !in_array($key, $this->accessibleProperties)
                ?: $this->$key = $value
        );
    }

    public function prepareMessage(): string
    {
        return hex2bin(self::getHeader()) . self::getPayload();
    }

    private function getHeader(): string
    {
        return config('lorawan.protocolVersion') .
            ($this->token = bin2hex(openssl_random_pseudo_bytes(2))) .
            Packet::PKT_PULL_RESP->value;
    }

    private function getPayload(): string
    {
        return json_encode([
            'txpk' => array_filter(
                get_object_vars($this),
                fn ($value, $key) => !is_null($value) && in_array($key, $this->accessibleProperties),
                ARRAY_FILTER_USE_BOTH
            ),
        ]);
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
