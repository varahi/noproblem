<?php
/*
/src/Service/Datahandler.php
*/

namespace App\Service;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class DataHandler implements MessageComponentInterface
{
    private $connections = [];

    public function onOpen(ConnectionInterface $conn)
    {
        echo "Connected new client with Id:" . $conn->resourceId . "\n";
        $this->connections[] = [
            'connection' => $conn,
            'identifier' => ''
        ];
        echo count($this->connections) . " active connections\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        echo "Closing Connection with Id:" . $conn->resourceId . "\n";
        foreach ($this->connections as $key => $connection) {
            if ($connection['connection']->resourceId == $conn->resourceId) {
                $connection['connection']->close();
                array_splice($this->connections, $key, 1);
            }
        }
        echo count($this->connections) . " active connections\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: " . $e->getMessage().$e->getLine() . "\n";
        $conn->close();
        echo count($this->connections) . " active connections\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        switch ($data['action']) {
            case 'register':

                foreach ($this->connections as &$conn) {
                    if ($conn['connection']->resourceId == $from->resourceId) {
                        $conn['identifier'] = $data['value'];
                    }
                }

                break;
            case 'message':
                $found = false;
                foreach ($this->connections as $conn) {
                    if ($conn['identifier'] == $data['to']) {
                        $conn['connection']->send(json_encode(['action'=>'message','value'=>$data['value']]));
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $from->send(json_encode(['action'=>'error','value'=>'User you are trying to connect is not online!']));
                }
                break;
            default:
                break;
        }
        echo "Message: " . $msg . "\nreceived from " . $from->resourceId . "\n";
    }
}
