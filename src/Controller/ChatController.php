<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatRoom;
use App\Entity\User;
use App\ImageOptimizer;
use App\Repository\UserRepository;
use App\Service\ModalForms;
use Doctrine\Persistence\ManagerRegistry;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ChatController extends AbstractController
{
    /**
     * @var string
     */
    private $defailtDomain;

    /**
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param ModalForms $modalForms
     * @param ImageOptimizer $imageOptimizer
     * @param string $targetDirectory
     */
    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms,
        ImageOptimizer $imageOptimizer,
        string $targetDirectory,
        string $defailtDomain
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->imageOptimizer = $imageOptimizer;
        $this->targetDirectory = $targetDirectory;
        $this->defailtDomain = $defailtDomain;
    }

    /**
     * @Route("/chat", name="app_chat")
     */
    public function chat(
        UserRepository $userRepository
    ) {
        $toUser = null;
        $fromUser = $this->getUser();
        $allUsers = $userRepository->findAllExcluded($fromUser, 'ROLE_SUPER_ADMIN');
        return $this->render('chat/index.html.twig', [
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'allUsers' => $allUsers
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

        if (!empty($chatRoom)) {
            $chatRoom = $chatRoom[0];
        } else {
            $chatRoom = null;
        }

        $allUsers = $userRepository->findAllExcluded($fromUser, 'ROLE_SUPER_ADMIN');
        return $this->render('chat/selected_chat.html.twig', [
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'allUsers' => $allUsers,
            'chatRoom' => $chatRoom,
            'fromImgSrc' => $fromImgSrc,
            'toImgSrc' => $toImgSrc
        ]);
    }

    /**
     * @Route("/user", name="check_user")
     *
     * @param Request $request
     */
    public function checkUser(Request $request)
    {
        $user = $this->security->getUser();
        $data = \json_decode($request->getContent(), true);

        file_put_contents('data.json', json_encode($data));
        file_put_contents('user.json', json_encode($data['userId']));
        file_put_contents('msg.json', json_encode($data['message']));

        $chat = new Chat();
        //$chat->setMessage(($data['message']));
        //$chat->setUserId($user->getId());
        $chat->setMessage('Some message');
        $chat->setUserId('Some user id');

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($chat);
        $entityManager->flush();

        //if (isset($data) && !empty($data) && !empty($data['msg'])) {
        //}

        return new JsonResponse("null", 200);
    }
}
