<?php

namespace App\Service;

use App\Controller\Traits\ChatTrait;
use App\Entity\Chat;
use App\Entity\ChatRoom;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\ChatRoomRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatMessenger extends AbstractController implements MessageComponentInterface
{
    //use ChatTrait;

    private $paths;

    private $activeUsers;

    private $activeConnections;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->users = new \SplObjectStorage();
        $this->paths = [];
        $this->activeUsers = [];
        $this->activeConnections = [];
        $this->doctrine = $doctrine;
    }

    public function onOpen(ConnectionInterface $socket)
    {
        // Attach new connection
        $this->users->attach($socket);
        echo "New user! ({$socket->resourceId})\n";
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

        echo sprintf(' User1 ' . $jsonMsg->fromId .' User2 '. $jsonMsg->toId . "\n");

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
            if ($chatRoom->getSocketId() === $conn->resourceId) {
                // echo sprintf('New socket id ' . $conn->resourceId);
                $chatRoom->setSocketId2((int)$conn->resourceId);
                $entityManager->persist($chatRoom);
                $entityManager->flush();
            } else {
                // echo sprintf('New socket id ' . $conn->resourceId);
                $chatRoom->setSocketId((int)$conn->resourceId);
                $entityManager->persist($chatRoom);
                $entityManager->flush();
            }
        }
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        //$numRecv = count($this->users) - 1;
        //echo sprintf('Connection %d sending message "%s" to %d other socketection%s' . "\n", $conn->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        //echo sprintf(' From id - ' . $jsonMsg->fromId .' Res id - '. $conn->resourceId . "\n");
        //echo sprintf(' To id - ' . $jsonMsg->toId .' Res id - '. $conn->resourceId . "\n");

        $jsonMsg = json_decode($msg);
        if ($jsonMsg->toId || $jsonMsg->fromId) {
            $this->updateChatRoom($conn, $msg);
        }

        $entityManager = $this->doctrine->getManager();
        $chatRoom = $this->getChatRoom($msg);

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

            foreach ($this->users as $user) {
                if ($conn !== $user) {
                    //echo sprintf(' Send message from user - ' . $conn->resourceId . "\n");
                    if ($conn->resourceId === $chatRoom->getSocketId() || $conn->resourceId === $chatRoom->getSocketId2()) {
                        $user->send($msg);
                    }
                }
            }
        }
    }

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
