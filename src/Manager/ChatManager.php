<?php

namespace App\Manager;

use App\Entity\Chat;
use App\Entity\ChatUser;
use Doctrine\Persistence\ManagerRegistry;

class ChatManager
{
    public function removeUserFromChat(
        ChatUser $user,
        Chat $chat,
        ManagerRegistry $doctrine
    ) {
        $entityManager = $doctrine->getManager();
        if ($chat->getIsCompleted()) {
            $chat->removeUser($user);
            $chat->setIsCompleted(false);
        } else {
            $entityManager->remove($chat);
        }
        $entityManager->remove($user);
        $entityManager->flush();
    }

    public function findOrCreateChatForUser(
        $rid,
        ManagerRegistry $doctrine
    ) {
        $entityManager = $doctrine->getManager();
        $chat_user = new ChatUser();
        $chat_user->setRid($rid);
        $chat = $this->getUncompletedChat();
        if ($chat) {
            $chat->setIsCompleted(true);
        } else {
            $chat = new Chat();
        }
        $chat_user->setChat($chat);
        $entityManager->persist($chat);
        $entityManager->persist($chat_user);
        $entityManager->flush();
        return $chat;
    }

    public function getChatByUser(
        $rid
    ) {
        $chat_user = $this->getUserByRid($rid);
        return $chat_user ? $chat_user->getChat() : null;
    }

    public function getUserByRid(
        $rid,
        ManagerRegistry $doctrine
    ) {
        $entityManager = $doctrine->getManager();
        return $entityManager->getRepository('ISMSChatBundle:ChatUser')->findOneBy(['rid' => $rid]);
    }

    public function getUncompletedChat(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        return $entityManager->getRepository('ISMSChatBundle:Chat')->findOneBy(['isCompleted' => false]);
    }

    public function truncateChats(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $entityManager->getConnection();
        $platform = $conn->getDatabasePlatform();
        $conn->query('SET FOREIGN_KEY_CHECKS=0');
        $conn->executeUpdate($platform->getTruncateTableSQL('chat_user'));
        $conn->executeUpdate($platform->getTruncateTableSQL('chat'));
        $conn->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
