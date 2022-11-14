<?php

namespace App\Controller;

use App\Entity\Chat;
use App\ImageOptimizer;
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
    public function index()
    {
        $user = $this->getUser();
        return $this->render('chat/index.html.twig', [
            'user' => $user
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
