<?php

namespace App\Controller;

use App\Http\Request\LoginRequest;
use App\Service\LoginValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $loginValidator;

    public function __construct(
        LoginValidator $loginValidator
    ) {
        $this->loginValidator = $loginValidator;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
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
     * @Route("/login-handler", name="app_login")
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
