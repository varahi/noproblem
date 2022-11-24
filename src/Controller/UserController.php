<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Entity\Worksheet;
use App\Entity\Job;
use App\Entity\City;
use App\ImageOptimizer;
use App\Repository\CityRepository;
use App\Repository\JobRepository;
use App\Repository\OrderRepository;
use App\Repository\WorksheetRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\ModalForms;
use App\Service\SessionService;
use App\Service\SignUpValidator;
use App\Service\UserCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    use AbstractTrait;

    use DataTrait;

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Time in seconds 3600 - one hour
     */
    public const CACHE_MAX_AGE = '3600';

    /**
     * @var Security
     */
    private $security;

    /**
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param ModalForms $modalForms
     * @param ImageOptimizer $imageOptimizer
     * @param string $targetDirectory
     * @param SessionService $sessionService
     */
    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms,
        ImageOptimizer $imageOptimizer,
        string $targetDirectory,
        SessionService $sessionService
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->imageOptimizer = $imageOptimizer;
        $this->targetDirectory = $targetDirectory;
        $this->sessionService = $sessionService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function signUp(Request $request): Response
    {
        return $this->render('profile/sign-up.html.twig');
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
        } elseif ($user != null && in_array(self::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return $this->redirectToRoute("admin");
        } else {
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     * Require ROLE_CUSTOMER for *every* controller method in this class.
     *
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/lk/customer", name="app_lk_customer")
     * @throws \Exception
     */
    public function customerProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        WorksheetRepository $worksheetRepository,
        OrderRepository $orderRepository
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            $worksheets = $worksheetRepository->findByUser($user->getId());

            // Check days left and set user status
            $order = $orderRepository->findByUserAndActive($user->getId());
            $daysLeft = $this->getDaysLeft($order, $user);

            // Get percents how profile filled to show modal warning window
            $persentFilled = $this->getPersentFilled($user);
            if ($persentFilled <= 0.6) {
                $profleFilled = 0;
            } else {
                $profleFilled = 1;
            }

            // Resize avatar if exist
            if ($user->getAvatar()) {
                $this->imageOptimizer->resize($this->targetDirectory.'/'.$user->getAvatar());
            }

            /*$cityName = $this->sessionService->getCity();
            $encoders = [new XmlEncoder(), new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);*/

            //$cityName = $this->sessionService->getCity();
            //dd($cityName);

            {
                return $this->render('user/lk_customer.html.twig', [
                    'user' => $user,
                    'order' => $order,
                    'daysLeft' => $daysLeft,
                    'worksheets' => $worksheets,
                    'profleFilled' => $profleFilled,
                    'persentFilled' => $persentFilled,
                    // Get City from session
                    //'cityName' => $this->sessionService->getCity(),
                    //'cityName'   => $serializer->normalize($cityName, 'json'),
                    // Modal form for all pages
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
     * @Route("/lk/employer", name="app_lk_employee")
     * @throws \Exception
     */
    public function employeeProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        JobRepository $jobRepository,
        OrderRepository $orderRepository
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            $jobs = $jobRepository->findByUser($user->getId());

            // Resize avatar if exist
            if ($user->getAvatar()) {
                $this->imageOptimizer->resize($this->targetDirectory.'/'.$user->getAvatar());
            }

            // Check days left and set user status
            $order = $orderRepository->findByUserAndActive($user->getId());
            $daysLeft = $this->getDaysLeft($order, $user);

            // Get percents how profile filled to show modal warning window
            $persentFilled = $this->getPersentFilled($user);
            if ($persentFilled <= 0.6) {
                $profleFilled = 0;
            } else {
                $profleFilled = 1;
            }

            {
                return $this->render('user/lk_customer.html.twig', [
                    'user' => $user,
                    'jobs' => $jobs,
                    'daysLeft' => $daysLeft,
                    'profleFilled' => $profleFilled,
                    'persentFilled' => $persentFilled,
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
     * @Route("/upload-cropped-image", name="app_upload_cropped_image")
     *
     * @return Response
     */
    public function uploadCroppedImage(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER) || $this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();

            if ($user->getAvatar()) {
                if (isset($_POST["image"])) {
                    $data = $_POST["image"];
                    $image_array_1 = explode(";", $data);
                    $image_array_2 = explode(",", $image_array_1[1]);
                    $data = base64_decode($image_array_2[1]);
                    $newImageName = $user->getUsername() .'-'. time() . '.png';
                    $user->setAvatar($newImageName);
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $targetImageName = $this->targetDirectory .'/'. $newImageName;
                    file_put_contents($targetImageName, $data);
                }
            }

            return new Response(
                'There is ok',
                Response::HTTP_OK
            );

            /*$message = $translator->trans('Image updated', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);*/
        }
    }

    /**
     * Require ROLE_BUYER for *every* controller method in this class.
     *
     * @IsGranted("ROLE_BUYER")
     * @Route("/lk/buyer", name="app_lk_buyer")
     */
    public function buyerProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        if ($this->isGranted(self::ROLE_BUYER)) {
            $user = $this->security->getUser();

            // Resize avatar if exist
            if ($user->getAvatar()) {
                $this->imageOptimizer->resize($this->targetDirectory.'/'.$user->getAvatar());
            }

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
     * @Route("/lk/edit-profile", name="app_edit_profile")
     */
    public function editProfile(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader $fileUploader,
        CityRepository $cityRepository
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

            if ($form->isSubmitted()) {
                $post = $request->request->get('edit_profile_form');
                $city = $cityRepository->findOneBy(['name' => $post['city']]);

                if ($city == null) {
                    // Create a new city if not exist in database
                    $city = $this->setNewCity($request, 'edit_profile_form');
                }

                $user->setCity($city);

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
                        $referer = $request->headers->get('referer');
                        return new RedirectResponse($referer);
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

    /**
     *
     * @Route("/add-to-favorite/worksheet-{id}", name="app_add_worksheet_to_favorite")
     */
    public function addWorksheetToFavorite(
        Request $request,
        Worksheet $worksheet
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();

            $worksheet->addFeaturedUser($user);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($worksheet);
            $entityManager->flush();

            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
    }

    /**
     *
     * @Route("/remove-from-favorite/worksheet-{id}", name="app_remove_worksheet_from_favorite")
     */
    public function removeWorksheetFromFavorite(
        Request $request,
        Worksheet $worksheet
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();

            $worksheet->removeFeaturedUser($user);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($worksheet);
            $entityManager->flush();

            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
    }


    /**
     *
     * @Route("/add-to-favorite/job-{id}", name="app_add_job_to_favorite")
     */
    public function addJobToFavorite(
        Request $request,
        Job $job
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();

            $job->addFeaturedUser($user);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($job);
            $entityManager->flush();

            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
    }

    /**
     *
     * @Route("/remove-from-favorite/job-{id}", name="app_remove_job_from_favorite")
     */
    public function removeJobFromFavorite(
        Request $request,
        Job $job
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();

            $job->removeFeaturedUser($user);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($job);
            $entityManager->flush();

            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
    }

    /**
     * @param $order
     * @param $user
     * @return null
     * @throws \Exception
     */
    private function getDaysLeft($order, $user)
    {
        if ($order !==null) {
            $currentDateStr = date('Y-m-d H:i:s');
            $currentDate = new \DateTime($currentDateStr);
            if ($order->getEndDate() !==null) {
                $daysLeft = $order->getEndDate()->diff($currentDate)->format("%a");
            }
            if (isset($daysLeft) && $daysLeft <= 0) {
                $user->setInactive(true);
                $user->setActive(false);
            } else {
                $user->setInactive(false);
                $user->setActive(true);
            }
        } else {
            $daysLeft = null;
        }

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $daysLeft;
    }

    /**
     * @param $user
     * @return float|int
     */
    private function getPersentFilled($user)
    {
        $fieldsArr = [
            'age' => $user->getAge(),
            'citizen' => $user->getCitizen(),
            'about' => $user->getAbout(),
            'avatar' => $user->getAvatar()
        ];

        $filledFieldsArr = array_filter($fieldsArr);
        $persentFilled = count($filledFieldsArr) / count($fieldsArr);

        return $persentFilled;
    }
}
