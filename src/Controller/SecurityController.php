<?php

namespace App\Controller;

use App\Entity\User;
use App\Http\Request\LoginRequest;
use App\Service\LoginValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $loginValidator;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        LoginValidator $loginValidator,
        SerializerInterface $serializer
    ) {
        $this->loginValidator = $loginValidator;
        $this->serializer = $serializer;
    }

    //@Route("/login", name="app_login", methods={"POST"})

    /**
     * @Route("/api/login", name="app_api_login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $user = $this->getUser();

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function loginBack(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @ParamConverter(
     *      "loginRequest",
     *      converter="fos_rest.request_body",
     *      class="App\Http\Request\LoginRequest"
     * )
     *
     * @param LoginRequest $loginRequest
     *
     * @return JsonResponse
     * @throws \Exception
     *
     * @Route("/login-handler", name="app_login_handler")
     */
    public function loginHandler(LoginRequest $loginRequest): JsonResponse
    {
        dd($loginRequest);

        if (!$this->loginValidator->validate($loginRequest)) {
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $this->loginValidator->getErrors()
            ]);
        }

        /*if ($this->getUser()) {
             return $this->redirectToRoute('app_articles');
        }*/

        //$user = $this->userCreator->createUser($signUpRequest);

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            //'entity' => $user->getId()
        ]);
    }
}
