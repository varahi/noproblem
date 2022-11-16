<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use App\Entity\ChatRoom;
use App\Entity\User;
use App\Repository\ChatRoomRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
trait ChatTrait
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
     * @param $jsonMsg
     * @return void
     */
    /*private function updateChatRoom(
        $jsonMsg
    ) {
        $entityManager = $this->doctrine->getManager();
        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->fromId]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => $jsonMsg->toId]);
        $users = (object) array_merge((array) $user1, (array) $user2);
        $chatRoom = $entityManager->getRepository(ChatRoom::class)->findOneByUsers((array)$users);
        if (count($chatRoom) < 0) {
            $chatRoom = new ChatRoom();
        }

        $entityManager->persist($chatRoom);
        $entityManager->flush();
    }*/

    public function getChatRoom($msg)
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
}
