<?php

namespace App\Console\Commands;

use App\Socket\DatagramFactory;
use App\Socket\DatagramSocket;
use App\Socket\DataHandler\ReceivedDataHandler;
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

            $server->on('message', function($message, $address, $server) {
                ReceivedDataHandler::handle($server, $address, $message);
            });
        });
    }
}
