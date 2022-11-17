<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatRoom;
use App\Entity\User;
use App\ImageOptimizer;
use App\Repository\UserRepository;
use App\Service\ModalForms;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ChatController extends AbstractController
{
    /**
     * @var string
     */
    private $defailtDomain;

    public function __construct(
        ManagerRegistry $doctrine,
        ModalForms $modalForms,
        string $defailtDomain
    ) {
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->defailtDomain = $defailtDomain;
    }

    /**
     * @Route("/chat", name="app_chat")
     */
    public function chat(
        Request $request,
        UserRepository $userRepository
    ) {
        $toUser = null;
        $fromUser = $this->getUser();
        $allUsers = $userRepository->findAllExcluded($fromUser, 'ROLE_SUPER_ADMIN');
        return $this->render('chat/index.html.twig', [
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'allUsers' => $allUsers,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
    }

    /**
     * @Route("/selected-chat/user-{id}", name="app_selected_chat")
     */
    public function selectedChat(
        Request $request,
        User $toUser,
        UserRepository $userRepository
    ) {
        $fromUser = $this->getUser();
        $entityManager = $this->doctrine->getManager();
        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => $fromUser->getId()]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => $toUser->getId()]);
        $chatRoom = $entityManager->getRepository(ChatRoom::class)->findOneByUsers($user1, $user2);

        if (!empty($chatRoom)) {
            $chatRoom = $chatRoom[0];
            $fromUser->setCurrentChatRoom($chatRoom->getId());
            $entityManager->persist($fromUser);
            $entityManager->flush();
        } else {
            $chatRoom = null;
        }

        if ($fromUser->getAvatar()) {
            $fromImgSrc = 'https://'.$this->defailtDomain. '/uploads/files/' . $fromUser->getAvatar();
        } else {
            $fromImgSrc = 'https://'.$this->defailtDomain. '/assets/img/ava_person_account.png';
        }

        if ($toUser->getAvatar()) {
            $toImgSrc = 'https://'.$this->defailtDomain. '/uploads/files/' . $toUser->getAvatar();
        } else {
            $toImgSrc = 'https://'.$this->defailtDomain. '/assets/img/ava_person_account.png';
        }

        $allUsers = $userRepository->findAllExcluded($fromUser, 'ROLE_SUPER_ADMIN');
        return $this->render('chat/selected_chat.html.twig', [
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'allUsers' => $allUsers,
            'chatRoom' => $chatRoom,
            'fromImgSrc' => $fromImgSrc,
            'toImgSrc' => $toImgSrc,
            'defailtDomain' => $this->defailtDomain,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
    }
}
