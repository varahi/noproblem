<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Job;
use App\Form\Job\JobFormType;
use App\Http\Request\SignUpRequest;
use App\Repository\JobRepository;
use App\Repository\WorksheetRepository;
use App\Service\FileUploader;
use App\Service\ModalForms;
use App\Service\SignUpValidator;
use App\Service\UserCreator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use App\Form\User\EditProfileFormType;

class UserController extends AbstractController
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    /**
     * @var ValidatorInterface
     */
    private $signUpValidator;

    /**
     * @var SignUpValidator
     */
    private $userCreator;

    private $security;

    /**
     * @param SignUpValidator $signUpValidator
     * @param UserCreator $userCreator
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param ModalForms $modalForms
     */
    public function __construct(
        SignUpValidator $signUpValidator,
        UserCreator $userCreator,
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms
    ) {
        $this->userCreator = $userCreator;
        $this->signUpValidator = $signUpValidator;
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
    }

    public function signUp(Request $request): Response
    {
        return $this->render('profile/sign-up.html.twig');
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
     * Method to redirect logged in user
     *
     * @Route("/lk", name="app_lk")
     */
    public function login(Request $request): Response
    {
        $user = $this->getUser();
        if ($user != null && in_array(self::ROLE_CUSTOMER, $user->getRoles())) {
            return $this->redirectToRoute("app_lk_customer");
        } elseif ($user != null && in_array(self::ROLE_EMPLOYEE, $user->getRoles())) {
            return $this->redirectToRoute("app_lk_employee");
        } elseif ($user != null && in_array(self::ROLE_BUYER, $user->getRoles())) {
            return $this->redirectToRoute("app_lk_buyer");
        } else {
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     * Require ROLE_CUSTOMER for *every* controller method in this class.
     *
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/lk-customer", name="app_lk_customer")
     */
    public function customerProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        WorksheetRepository $worksheetRepository
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            $worksheets = $worksheetRepository->findByUser($user->getId());
            {
                return $this->render('user/lk_customer.html.twig', [
                    'user' => $user,
                    'worksheets' => $worksheets,
                    'ticketForm' => $this->modalForms->ticketForm($request)->createView()
                ]);
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     * Require ROLE_EMPLOYEE for *every* controller method in this class.
     *
     * @IsGranted("ROLE_EMPLOYEE")
     * @Route("/lk-employee", name="app_lk_employee")
     */
    public function employeeProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        JobRepository $jobRepository
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            $jobs = $jobRepository->findByUser($user->getId());
            {
                return $this->render('user/lk_customer.html.twig', [
                    'user' => $user,
                    'jobs' => $jobs,
                    'ticketForm' => $this->modalForms->ticketForm($request)->createView()
                ]);
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     * Require ROLE_BUYER for *every* controller method in this class.
     *
     * @IsGranted("ROLE_BUYER")
     * @Route("/lk-buyer", name="app_lk_buyer")
     */
    public function buyerProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        if ($this->isGranted(self::ROLE_BUYER)) {
            $user = $this->security->getUser();
            {
                return $this->render('user/lk_customer.html.twig', [
                    'user' => $user,
                    'ticketForm' => $this->modalForms->ticketForm($request)->createView()
                ]);
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     *
     * @Route("/edit-profile", name="app_edit_profile")
     */
    public function editProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader $fileUploader
    ): Response {
        if (
            $this->isGranted(self::ROLE_BUYER) ||
            $this->isGranted(self::ROLE_CUSTOMER) ||
            $this->isGranted(self::ROLE_EMPLOYEE)
        ) {
            $user = $this->security->getUser();
            $form = $this->createForm(EditProfileFormType::class, $user);
            $form->handleRequest($request);
            $entityManager = $this->doctrine->getManager();

            if ($form->isSubmitted() && $form->isValid()) {
                $post = $request->request->get('edit_profile_form');
                // Set new password if changed
                if ($post['plainPassword']['first'] !=='' && $post['plainPassword']['second'] !=='') {
                    if (strcmp($post['plainPassword']['first'], $post['plainPassword']['second']) == 0) {
                        // encode the plain password
                        $user->setPassword(
                            $passwordHasher->hashPassword(
                                $user,
                                $post['plainPassword']['first']
                            )
                        );
                    } else {
                        $message = $translator->trans('Mismatch password', array(), 'flash');
                        $notifier->send(new Notification($message, ['browser']));
                        return $this->redirectToRoute("app_edit_client_profile");
                    }
                }

                // File upload
                $avatarFile = $form->get('avatar')->getData();
                if ($avatarFile) {
                    $avatarFileName = $fileUploader->upload($avatarFile);
                    $user->setAvatar($avatarFileName);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $message = $translator->trans('Profile updated', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return new Response($this->twig->render('user/edit_profile.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]));
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }
}
