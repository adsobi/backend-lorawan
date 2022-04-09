<?php

namespace App\Socket;

use Evenement\EventEmitterInterface;

/**
 * interface very similar to React\Stream\Stream
 *
 * @event message($data, $remoteAddress, $thisSocket)
 * @event error($exception, $thisSocket)
 * @event close($thisSocket)
 */
interface DatagramSocketInterface extends EventEmitterInterface
{
    public function send($data, $remoteAddress = null);

    public function close();

    public function end();

    public function resume();

    public function pause();

    public function getLocalAddress();

    public function getRemoteAddress();
}
