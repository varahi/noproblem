<?php
/*
/src/Service/Datahandler.php
*/

namespace App\Service;

use App\Entity\Chat;
use App\Entity\ChatRoom;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ChatMessenger implements MessageComponentInterface
{
    private $connections = [];

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

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

        //file_put_contents('data.json', json_encode($data));
        //echo sprintf(' To user ' . $data);

        switch ($data['action']) {
            case 'register':
                foreach ($this->connections as &$conn) {
                    if ($conn['connection']->resourceId == $from->resourceId) {
                        $conn['identifier'] = $data['value'];
                    }
                }

                echo sprintf(' Action ' . $data['action']);
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
                    $from->send(json_encode(['action'=>'error','value'=>'Пользователь сейчас не в сети! Он прочитает ваше сообщение когда ввойдет в систему']));
                }

                // Save chat to database
                $this->updateChatRoom($from, $msg);
                $chatRoom = $this->getChatRoom($msg);
                if ($chatRoom !==null) {
                    $this->saveChatMessage($chatRoom, $msg);
                }

                echo sprintf(' Action ' . $data['action']);
                break;
            default:
                break;
        }
        echo "Message: " . $msg . "\nreceived from " . $from->resourceId . "\n";
    }

    /**
     * @param $jsonMsg
     * @return void
     */
    private function updateChatRoom(
        $conn,
        $msg
    ) {
        $data = json_decode($msg, true);
        $entityManager = $this->doctrine->getManager();
        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['from']]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['to']]);

        $chatRoom = $this->getChatRoom($msg);

        if ($chatRoom == null) {
            // New chat room
            // echo sprintf('New chat created');
            $chatRoom = new ChatRoom();
            $chatRoom->setSocketId($conn->resourceId);
            //$chatRoom->setSocketId2($conn->resourceId);
            $chatRoom->addUser($user1);
            $chatRoom->addUser($user2);
            $entityManager->persist($chatRoom);
            $entityManager->flush();
        } else {
            // Update existing chat room
            $chatRoom->setSocketId((int)$conn->resourceId);
            $entityManager->persist($chatRoom);
            $entityManager->flush();
        }
    }

    /**
     * @param $msg
     * @return null
     */
    private function getChatRoom($msg)
    {
        $data = json_decode($msg, true);
        $entityManager = $this->doctrine->getManager();
        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['from']]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['to']]);
        $chatRoom = $entityManager->getRepository(ChatRoom::class)->findOneByUsers($user1, $user2);
        if (!empty($chatRoom)) {
            $chatRoom = $chatRoom[0];
        } else {
            $chatRoom = null;
        }

        return $chatRoom;
    }

    /**
     * @param $chatRoom
     * @param $msg
     * @return void
     */
    private function saveChatMessage($chatRoom, $msg)
    {
        $data = json_decode($msg, true);
        $entityManager = $this->doctrine->getManager();

        if ($chatRoom !==null) {
            $sender = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['from']]);
            $reciever = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['to']]);

            $chat = new Chat();
            $chat->setMessage($data['value']);
            $chat->setChatRoom($chatRoom);
            $chat->setSender($sender);
            $chat->setReciever($reciever);
            $entityManager->persist($chat);
            $entityManager->flush();
        }
    }
}
