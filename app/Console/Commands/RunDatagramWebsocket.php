<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use React\Datagram\Factory;
use React\Datagram\Socket;

class RunDatagramWebsocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs websocket send and receive datagram';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $factory = new Factory();

        $factory->createServer(env('WEBSOCKET_URL_AND_PORT'))->then(function (Socket $server) {
            $server->bufferSize = 4000;
            echo "Run websocket server on xd";
            $server->on('message', function($message, $address, $server) {
                //$server->send('hello ' . $address . '! echo: ' . $message, $address);

                echo 'client ' . $address . ': ' . $message . PHP_EOL;
            });
        });
    }
}
