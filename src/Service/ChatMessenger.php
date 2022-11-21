<?php

namespace App\Service;

use App\Controller\Traits\ChatTrait;
use App\Entity\Chat;
use App\Entity\ChatRoom;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatMessenger extends AbstractController implements MessageComponentInterface
{
    //use ChatTrait;

    private $connections =  [];

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->users = new \SplObjectStorage();
        $this->connections = [];
        $this->doctrine = $doctrine;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onOpen(ConnectionInterface $conn)
    {
        // Attach new connection
        $this->users->attach($conn);
        $this->connections[] = $conn;

        echo "Connected new client with Id:".$conn->resourceId."\n";
        echo count($this->connections)." active connections\n";

        echo "New user! ({$conn->resourceId})\n";

        /* if (isset($this->users)) {
             foreach ($this->users as $user) {
                 if ($conn !== $user) {
                     echo sprintf(' On open - User id ' . $user->resourceId . ' Conn id ' . $conn->resourceId . "\n");
                 }
             }
         }*/

        // ToDo: Find chat room and set only one socket of new user
        //$entityManager = $this->doctrine->getManager();
        //$chatRoom = $entityManager->getRepository(ChatRoom::class)->findOneBy(['id' => $socket->resourceId]);
        //$chatRoom = $this->getChatRoom($msg);
    }

    /**
     * @param ConnectionInterface $conn
     * @param $msg
     * @return void
     */
    public function onMessage(ConnectionInterface $conn, $msg)
    {
        // $jsonMsg = json_decode($msg);
        //if ($jsonMsg->toId || $jsonMsg->fromId) {
        //$this->updateChatRoom($conn, $msg);
        //}
        $this->updateChatRoom($conn, $msg);
        $chatRoom = $this->getChatRoom($msg);
        $entityManager = $this->doctrine->getManager();

        if ($chatRoom !==null) {
            $this->saveChatMessage($chatRoom, $msg);
            foreach ($this->users as $user) {

                //$chatRoom->setSocketId((int)$user->resourceId);
                //$entityManager->persist($chatRoom);
                //$entityManager->flush();

                echo sprintf(' User id ' . $user->resourceId . ' Conn id ' . $conn->resourceId . ' Chat room '. $chatRoom->getId() . "\n");
                if ($conn !== $user) {
                    if ($user->resourceId == $chatRoom->getSocketId()) {
                        $user->send($msg);
                    }
                }
            }
        }
    }

    /**
     * @param ConnectionInterface $socket
     * @return void
     */
    public function onClose(ConnectionInterface $socket)
    {
        $this->users->detach($socket);
        echo "Connection {$socket->resourceId} was terminated\n";
    }

    /**
     * @param ConnectionInterface $socket
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $socket, \Exception $e)
    {
        echo "You got an error: {$e->getMessage()}\n";
        $socket->close();
    }

    /**
     * @param $msg
     * @return null
     */
    private function getChatRoom($msg)
    {
        $jsonMsg = json_decode($msg);
        $entityManager = $this->doctrine->getManager();
        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->fromId]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->toId]);
        $chatRoom = $entityManager->getRepository(ChatRoom::class)->findOneByUsers($user1, $user2);
        if (!empty($chatRoom)) {
            $chatRoom = $chatRoom[0];
        } else {
            $chatRoom = null;
        }

        return $chatRoom;
    }

    /**
     * @param $jsonMsg
     * @return void
     */
    private function updateChatRoom(
        $conn,
        $msg
    ) {
        $jsonMsg = json_decode($msg);
        $entityManager = $this->doctrine->getManager();
        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->fromId]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->toId]);

        $chatRoom = $this->getChatRoom($msg);
        //echo sprintf(' User1 ' . $jsonMsg->fromId .' User2 '. $jsonMsg->toId . "\n");

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
     * @param $chatRoom
     * @param $msg
     * @return void
     */
    private function saveChatMessage($chatRoom, $msg)
    {
        $jsonMsg = json_decode($msg);
        $entityManager = $this->doctrine->getManager();

        if ($chatRoom !==null) {
            $sender = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->fromId]);
            $reciever = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->toId]);

            $chat = new Chat();
            $chat->setMessage($jsonMsg->message);
            $chat->setChatRoom($chatRoom);
            $chat->setSender($sender);
            $chat->setReciever($reciever);
            $entityManager->persist($chat);
            $entityManager->flush();
        }
    }
}
