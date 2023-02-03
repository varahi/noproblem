<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Http\Request\SignUpRequest;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use App\Service\SignUpValidator;
use App\Service\UserCreator;
use App\Service\Recaptcha;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\NotifierInterface;
use App\Repository\UserRepository;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    /**
     * @var ValidatorInterface
     */
    private $signUpValidator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    private $recaptcha;

    private $translator;

    private $notifier;


    public function __construct(
        EmailVerifier $emailVerifier,
        SignUpValidator $signUpValidator,
        UserCreator $userCreator,
        RequestStack $requestStack,
        Recaptcha $recaptcha,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        VerifyEmailHelperInterface $helper
    ) {
        $this->signUpValidator = $signUpValidator;
        $this->userCreator = $userCreator;
        $this->requestStack = $requestStack;
        $this->recaptcha = $recaptcha;
        $this->translator = $translator;
        $this->notifier = $notifier;
        $this->emailVerifier = $emailVerifier;
        $this->verifyEmailHelper = $helper;
    }

    /**
     * @ParamConverter(
     *      "signUpRequest",
     *      converter="fos_rest.request_body",
     *      class="App\Http\Request\SignUpRequest"
     * )
     *
     * @param SignUpRequest $signUpRequest
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function signUpHandlerEmployer(SignUpRequest $signUpRequest): JsonResponse
    {
        if (!$this->signUpValidator->validate($signUpRequest)) {
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $this->signUpValidator->getErrors()
            ]);
        }

        $role = array('ROLE_EMPLOYEE');
        $user = $this->userCreator->createUser($signUpRequest, $role);

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'entity' => $user->getId()
        ]);
    }

    /**
     * @ParamConverter(
     *      "signUpRequest",
     *      converter="fos_rest.request_body",
     *      class="App\Http\Request\SignUpRequest"
     * )
     *
     * @param SignUpRequest $signUpRequest
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function signUpHandlerCustomer(SignUpRequest $signUpRequest): JsonResponse
    {

        $captchaEnable = 0;

        // Re-captcha validation
        if ($this->requestStack->getCurrentRequest()->getContent() && $captchaEnable === 1) {
            $request = $this->requestStack->getCurrentRequest()->getContent();
            $data = json_decode($request, true);

            //$token = $data['recaptchaToken'];
            //file_put_contents('/app/public_html/test.txt', $data['recaptchaToken']);
            //exit();

            $response = $this->recaptcha->verifyResponse($data['recaptchaToken']);
            if (isset($response['success']) && $response['success'] != true) {
                return new JsonResponse([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'errors' => $this->signUpValidator->getErrors()
                ]);
            }
        }

        if (!$this->signUpValidator->validate($signUpRequest)) {
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $this->signUpValidator->getErrors()
            ]);
        }

        $role = array('ROLE_CUSTOMER');
        $user = $this->userCreator->createUser($signUpRequest, $role);

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'entity' => $user->getId()
        ]);
    }

    /**
     * @ParamConverter(
     *      "signUpRequest",
     *      converter="fos_rest.request_body",
     *      class="App\Http\Request\SignUpRequest"
     * )
     *
     * @param SignUpRequest $signUpRequest
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function signUpHandlerBuyer(SignUpRequest $signUpRequest): JsonResponse
    {
        if (!$this->signUpValidator->validate($signUpRequest)) {
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $this->signUpValidator->getErrors()
            ]);
        }

        $role = array('ROLE_BUYER');
        $user = $this->userCreator->createUser($signUpRequest, $role);

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'entity' => $user->getId()
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setUsername($form->get('email')->getData());
            $user->setRoles(array('ROLE_EMPLOYEE'));
            $user->setIsVerified('1');

            $entityManager->persist($user);
            $entityManager->flush();

            // Verify email
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()] // add the user's id as an extra query param
            );

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('webmaster@noproblem.ru', 'Admin'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'verifyUrl' => $signatureComponents->getSignedUrl()
                    ])
            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //$user = $this->getUser();
        $id = $request->get('id'); // retrieve the user id from the url

        // Verify the user id exists and is not null
        if (null === $id) {
            $message = $translator->trans('Something wrong', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            $message = $translator->trans('Something wrong', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute('app_login');
        }

        // Do not get the User's Id or Email Address from the Request object
        try {
            //$this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $e) {

            //$this->addFlash('verify_email_error', $e->getReason());

            $message = $translator->trans('Something wrong', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute('app_login');
        }

        $message = $translator->trans('Email verifyed', array(), 'flash');
        $notifier->send(new Notification($message, ['browser']));
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/verify/email-back", name="app_verify_email_back")
     */
    public function _verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
