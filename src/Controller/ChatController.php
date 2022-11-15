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
        string $targetDirectory
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->imageOptimizer = $imageOptimizer;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @Route("/chat", name="chat")
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

        /*$res1 = '3574';
        $res2 = '970';

        $entityManager = $this->doctrine->getManager();

        $user1 = $entityManager->getRepository(User::class)->findOneBy(['id' => 9]);
        $user2 = $entityManager->getRepository(User::class)->findOneBy(['id' => 8]);
        $chatRoom = $entityManager->getRepository(ChatRoom::class)->findOneByUsers($user1, $user2);

        if(!empty($chatRoom)) {
            $chatRoom = $chatRoom[0];
        } else {
            $chatRoom = null;
        }

        dd($chatRoom->getSocketId2());

        if (empty($chatRoom) || $chatRoom !==null)  {
            // New chat room
            echo sprintf('New chat created');
            $chatRoom = new ChatRoom();
            $chatRoom->setSocketId(3574);
            $chatRoom->addUser($user1);
            $chatRoom->addUser($user2);
            $entityManager->persist($chatRoom);
            $entityManager->flush();
        } else {
            // Update existing chat room
            if($chatRoom->getSocketId() === '3574') {
                echo sprintf('New socket 2 id ' . 970);
                $chatRoom->setSocketId((int)970);
                $entityManager->persist($chatRoom);
                $entityManager->flush();
            } else {
                echo sprintf('New socket id ' . 970);
                $chatRoom->setSocketId2((int)970);
                $entityManager->persist($chatRoom);
                $entityManager->flush();
            }
        }*/


        $fromUser = $this->getUser();
        $allUsers = $userRepository->findAllExcluded($fromUser, 'ROLE_SUPER_ADMIN');
        return $this->render('chat/selected_chat.html.twig', [
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'allUsers' => $allUsers
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
