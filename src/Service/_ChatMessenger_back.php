<?php

namespace App\Service;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class _ChatMessenger_back extends AbstractController implements MessageComponentInterface
{
    private $paths;

    public function __construct()
    {
        $this->users = new \SplObjectStorage();
        $this->paths = [];
    }

    public function onOpen(ConnectionInterface $socket)
    {
        // Attach new connection
        $this->users->attach($socket);
        echo "New user! ({$socket->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->users) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other socketection%s' . "\n", $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->users as $user) {
            if ($from !== $user) {
                $user->send($msg);
            }
        }

        $tempArr = $this->paths;
        $reqArr = json_decode($msg, true);
        $tempArr[$from->resourceId]=$reqArr['sessionUserId'];
        $this->paths=$tempArr;

        file_put_contents('temp.json', json_encode($tempArr));

        // search in array
        $tempSearch= array_search($reqArr['sessionUserId'], $this->paths);
        if ($tempSearch) {
            $val=$tempSearch;
        } else {
            $val = $from->resourceId;
        }

        // curl code
        $request = "http://localhost/user";
        $header_array = array(
            'Content-Type:application/json',
            'Accept:application/json');

        // initiate curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $retData = json_decode($response);
    }

    public function onClose(ConnectionInterface $socket)
    {
        $this->users->detach($socket);
        echo "Connection {$socket->resourceId} was terminated\n";
    }

    public function onError(ConnectionInterface $socket, \Exception $e)
    {
        echo "You got an error: {$e->getMessage()}\n";
        $socket->close();
    }
}
