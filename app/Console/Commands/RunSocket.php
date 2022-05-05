<?php

namespace App\Console\Commands;

use App\Socket\DatagramFactory;
use App\Socket\DatagramSocket;
use App\Socket\DataHandler\ReceiveDataHandler;
use App\Socket\Message\PullRespMessage;
use Illuminate\Console\Command;

use React\Datagram\Factory;

class RunSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socket:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs socket send and receive datagram';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $factory = new DatagramFactory();
        $factory->createServer(env('SOCKET_URL_AND_PORT'))->then(function (DatagramSocket $server) {
            echo "Socket run on " . $server->getRemoteAddress() . "\n";

            $server->on('message', function ($message, $address, $server) {
                $receivedMessage = new ReceiveDataHandler();
                $receivedMessage->handle($server, $address, $message);

                if ($status) {
                    ($sendMessage = new PullRespMessage())->setAttributes([
                        'imme' => true,
                        'freq' => 864.1,
                        'rfch' => 0,
                        'powe' => 14,
                        'modu' => 'LORA',
                        'datr' => 'SF11BW125',
                        'codr' => '4/6',
                        'ipol' => false,
                        'size' => 32,
                        'data' => base64_encode(hex2bin("1E074E77028251DD7A40CEE2DFB950E69F3A02D317FB214C0F60CCA7"))
                    ]);
                    sleep(1);
                    $server->send($sendMessage->prepareMessage(), $address);
                    dump('SEEEEENDED');
                    $msg = hex2bin('02a4b803') . '{"txpk":{"imme":true,"freq":864.1,"rfch":0,"powe":14,"modu":"LORA","datr":"SF11BW125","codr":"4/6","ipol":false,"size":32,"data":'. base64_encode(hex2bin("1E074E77028251DD7A40CEE2DFB950E69F3A02D317FB214C0F60CCA7")) . '}}';
                }
            });
        });
    }
}
