<?php

namespace App\Controller;

use App\Entity\User;
use App\Http\Request\LoginRequest;
use App\Service\LoginValidator;
use App\Service\SessionService;
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
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var ValidatorInterface
     */
    private $loginValidator;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        LoginValidator $loginValidator,
        SerializerInterface $serializer,
        SessionService $sessionService
    ) {
        $this->loginValidator = $loginValidator;
        $this->serializer = $serializer;
        $this->sessionService = $sessionService;
    }

    /**
     * @Route("/api/login", name="app_api_login", methods={"POST"})
     */
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $user = $this->getUser();
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error !==null) {
            $errorMessage = $error->getMessage();
        } else {
            $errorMessage = null;
        }

        $data = $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'error' => $errorMessage
        ]);

        //file_put_contents('login.json', json_encode($data));
        return $data;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function pageLogin(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $this->getUser();
        if ($user != null && in_array(self::ROLE_CUSTOMER, $user->getRoles())) {
            return $this->redirectToRoute("app_lk_customer");
        } elseif ($user != null && in_array(self::ROLE_EMPLOYEE, $user->getRoles())) {
            return $this->redirectToRoute("app_lk_employee");
        } elseif ($user != null && in_array(self::ROLE_BUYER, $user->getRoles())) {
            return $this->redirectToRoute("app_lk_buyer");
        } elseif ($user != null && in_array(self::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return $this->redirectToRoute("admin");
        } else {
            return $this->render(
                'security/login.html.twig',
                [
                    'cityName' => $this->sessionService->getCity(),
                    'last_username' => $lastUsername,
                    'error' => $error
                ]
            );
        }
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
