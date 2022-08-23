<?php

namespace App\Console\Commands;

use App\Socket\DatagramFactory;
use App\Socket\DatagramSocket;
use App\Socket\DataHandler\BasePackageHandler;
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
                $response = $receivedMessage->handle($server, $address, $message);

                if ($response instanceof BasePackageHandler) {
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
                        'data' => $response->createResponse()
                    ]);
                    sleep(1);
                    $server->send($sendMessage->prepareMessage(), $address);
                    $server->send($sendMessage->prepareMessage(), $address);
                }
            });
        });
    }
}
