<?php

namespace App\Service;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatMessenger extends AbstractController implements MessageComponentInterface
{
    private $paths;

    private $activeUsers;

    private $activeConnections;

    public function __construct()
    {
        $this->users = new \SplObjectStorage();
        $this->paths = [];
        $this->activeUsers = [];
        $this->activeConnections = [];
    }

    public function onOpen(ConnectionInterface $socket)
    {
        // Attach new connection
        $this->users->attach($socket);
        echo "New user! ({$socket->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $numRecv = count($this->users) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other socketection%s' . "\n", $conn->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        //echo sprintf('Connection id' . $conn->resourceId);

        //$jsonMsg = json_decode($msg);
        //$this->activeUsers[$conn->resourceId] = $jsonMsg->fromUserId;

        // ToDo: set
        //$this->updateSocketId($jsonMsg->name,$conn->resourceId);

        /* Logic
        if( user logged in) {
            Set or update socket id user, $conn->resourceId
        } else {
            sendMessageToUser (user, $conn) - where user->resourceId == $toSocketId
        }
        */

        foreach ($this->users as $user) {
            //$toSocketId = should be
            if ($conn !== $user) {
                $user->send($msg);
                //echo sprintf('User res id' . $user->resourceId);
                //if ($user->resourceId == $toSocketId) {
                //}
            }
        }

        /*
        $tempArr = $this->paths;
        $reqArr = json_decode($msg, true);
        $tempArr[$conn->resourceId]=$reqArr['sessionUserId'];
        $this->paths=$tempArr;

        file_put_contents('temp.json', json_encode($tempArr));

        // search in array
        $tempSearch= array_search($reqArr['sessionUserId'], $this->paths);
        if ($tempSearch) {
            $val=$tempSearch;
        } else {
            $val = $conn->resourceId;
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
        $retData = json_decode($response);*/
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
