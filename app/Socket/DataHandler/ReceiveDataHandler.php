<?php

namespace App\Socket\DataHandler;

use App\Enums\MType;
use App\Enums\Packet;
use App\Models\Downlink;
use App\Models\EndNode;
use App\Models\Gateway;
use App\Models\HistoricalData;
use App\Socket\DatagramSocket;
use App\Socket\Message\PullDataMessage;
use App\Socket\Message\PushDataMessage;
class ReceiveDataHandler
{
    protected $fCntUp = 0;
    private function reverseHex(string $value): string
    {
        return join(array_reverse(str_split($value, 2)));
    }
    public function handle(
        DatagramSocket $server,
        string $address,
        string $message
    ) {
        switch (bin2hex(substr($message, 3, 1))) {
            case Packet::PKT_PUSH_DATA->value:
                $messageObj = new PushDataMessage($message);
                $response = hex2bin($messageObj->protocolVersion . $messageObj->token . Packet::PKT_PUSH_ACK->value);
                $server->send($response, $address);

                if (isset($messageObj->payload['rxpk']))
                {

                    $data = base64_decode($messageObj->payload['rxpk'][0]['data']);
                    $phyPayload = bin2hex($data);
                    switch (self::getMType(substr($phyPayload, 0, 2)))
                    {
                        case MType::JOIN_REQUEST->value:

                            $joinRequest = new JoinRequestHandler($phyPayload);
                            $endNode = EndNode::where('dev_eui',strtoupper($joinRequest->devEUI))->first();
                            $gateway = Gateway::where('gateway_eui',strtoupper($messageObj->gatewayMAC))->first();
                            if(!$endNode || !$gateway)
                            {
                                break;
                            }
                            HistoricalData::create([
                               'end_node_id' => $endNode->id,
                               'gateway_id' =>$gateway->id,
                               'data' => $messageObj->payload['rxpk'][0]['data'],
                               'type' => 'JoinRequest',
                           ]);
                            $obj = new JoinAcceptHandler($joinRequest->devNonce, $endNode->app->key);
                            $endNode->update([
                                'dev_addr' => $obj->devAddr,
                                'nwk_s_key' => $obj->nwkSKey,
                                'app_s_key' => $obj->appSKey,
                            ]);
                             Downlink::create([
                                'gateway_id' => $gateway->id,
                                'end_node_id' => $endNode->id,
                                'data' => $obj->createResponse(),
                                'tmst' => $messageObj->payload['rxpk'][0]['tmst'],
                                'freq' => $messageObj->payload['rxpk'][0]['freq'],
                                'modu' => $messageObj->payload['rxpk'][0]['modu'],
                                'datr' => $messageObj->payload['rxpk'][0]['datr'],
                                'codr' => $messageObj->payload['rxpk'][0]['codr'],
                                'type' => 'JoinAccept'
                             ]);
                            break;
                        case MType::UNCONFIRMED_DATA_UP->value:
                            $messageObj = new PushDataMessage($message);
                            $preparedData = $this->preparePacketForDecryption($messageObj);
                            $endNode = EndNode::where('dev_addr', self::reverseHex($preparedData['devAddr']))->first();
                            $gateway = Gateway::where('gateway_eui',strtoupper($messageObj->gatewayMAC))->first();
                            if(!$endNode || !$gateway)
                            {
                                break;
                            }
                            $crypter = new loraCrypto($preparedData['fcntUp'] == 0?$endNode->nwk_s_key: $endNode->app_s_key,$preparedData['devAddr']);
                            HistoricalData::create([
                                'end_node_id' => $endNode->id,
                                'gateway_id' =>$gateway->id,
                                'data' => $crypter->decrypt($preparedData['data'], $preparedData['fcntUp']),
                                'type' => "Uplink",
                                'snr' => $messageObj->payload['rxpk'][0]['lsnr'],
                                'rssi'=> $messageObj->payload['rxpk'][0]['rssi'],
                            ]);
                            break;
                    }
                }
                break;

            case Packet::PKT_PULL_DATA->value:
                $messageObj = new PullDataMessage($message);
                $gateway = Gateway::where('gateway_eui',strtoupper($messageObj->gatewayMAC))->first();
                if(!$gateway)
                {
                    break;
                }
                $response = hex2bin($messageObj->protocolVersion . $messageObj->token . Packet::PKT_PULL_ACK->value);
                $server->send($response, $address);
                $downlink = Downlink::where('gateway_id', $gateway->id)->first();
                if($downlink){
                    $response2 = json_encode(['txpk'=>[
                        'imme' => false,
                        'tmst' => $downlink->tmst + 5000000,
                        'freq' => $downlink->freq,
                        'rfch' => 0,
                        'powe' => 14,
                        'modu' => $downlink->modu,
                        'datr' => $downlink->datr,
                        'codr' => $downlink->codr,
                        'ipol' => true,
                        'size' => 33,
                        'ncrc'=> true,
                        'data' => $downlink->data
                     ]], JSON_UNESCAPED_SLASHES);

                     HistoricalData::create([
                        'end_node_id' => $downlink->end_node_id,
                        'gateway_id' =>$gateway->id,
                        'data' => $downlink->data,
                        'type' => $downlink->type,
                    ]);
                    $server->send(hex2bin($this->getPullResponseHeader()) . $response2, $address);
                    $downlink->delete();
                }
                break;
        }
        return false;
    }

    public static function getMType(string $mhdr): int
    {
        return (hexdec($mhdr) & 0xff) >> 5;
    }

    public function getPullResponseHeader(): string
    {
        return config('lorawan.protocolVersion') .
            ($this->token = bin2hex(openssl_random_pseudo_bytes(2))) .
            Packet::PKT_PULL_RESP->value;
    }
    public function preparePacketForDecryption($msgObj): array
    {
        $macPayload = substr_replace(substr_replace(bin2hex(base64_decode($msgObj->payload['rxpk'][0]['data'])),"", -8),"",0,2);
        $devAddr = self::reverseHex(substr($macPayload, 0 ,8));
        $foptslen =  hexdec(substr($macPayload, 9 ,1));
        $fcntUp = hexdec(self::reverseHex(substr($macPayload, 10 ,4)));
        $fport = hexdec(self::reverseHex(substr($macPayload,14+($foptslen*2),2)));
        $data = substr($macPayload, -(strlen($macPayload)-16-($foptslen*2)));
        return [
            'devAddr' => $devAddr,
            'fcntUp'=> $fcntUp,
            'fport' => $fport,
            'data' => $data,
        ];
    }
}