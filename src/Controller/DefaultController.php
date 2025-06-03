<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Service\ReviewManager;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function __construct(
        private ReviewManager $reviewManager
    ) {}

    #[Route('/login', name: 'login', methods: ['GET'])]
    public function login(Request $request, LoggerInterface $logger): RedirectResponse
    {
        $logger->info('Login action triggered');
        // Retrieve the base return URL from parameters (defined in .env)
        $baseUrl = rtrim($this->getParameter('cas_service_base_url'), '/');

        // Prepare the full return URL including the /force path
        $target = urlencode($baseUrl . '/force');

        // Construct the CAS login URL with the service parameter
        $url = 'https://' . 'cas-preprod.ccsd.cnrs.fr'
            . '/cas/login?service=' . $target;

        $logger->info('Redirecting to CAS login URL', ['url' => $url]);
        // Redirect the user to the CAS login page
        return $this->redirect($url);
    }


    #[Route('/logout', name:'logout', methods: ['GET'])]
    public function logout(Request $request, LoggerInterface $logger) {
        if (($this->getParameter('cas_logout_target') !== null) && (!empty($this->getParameter('cas_logout_target')))) {
            $logger->info('Logging out with redirect', ['target' => $this->getParameter('cas_logout_target')]);
            \phpCAS::logoutWithRedirectService($this->getParameter('cas_logout_target'));
        } else {
            $logger->info('Logging out without redirect');
            \phpCAS::logout();
        }
    }

    #[Route('/force', name:'force', methods: ['GET'])]
    public function force(Request $request, LoggerInterface $logger) : RedirectResponse
    {
        if ($this->getParameter("cas_gateway")) {
            $logger->info('Gateway mode is enabled');

            if (!isset($_SESSION)) {
                session_start();
                $logger->info('Session started manually');
            }

            session_destroy();
            $logger->info('Session destroyed due to gateway mode');
        }
        dump('=== DEBUG FORCE ACTION ===');

        $user = $this->getUser();

        //dd('User before CAS login:', $user);
        dump('User:', $this->getUser());
        $logger->info('User after CAS login', ['user' => $user ? $user->getUserIdentifier() : 'null']);

        return $this->redirectToRoute('user_profile');
    }

    #[Route('/', name:'index', methods: ['GET'])]
    public function index(Request $request, LoggerInterface $logger,PaginatorInterface $paginator) : Response
    {
        $logger->info('Home page accessed');
        $user = $this->getUser();
        if ($user) {
            $logger->info('User is authenticated', ['user' => $user->getUserIdentifier()]);
        } else {
            $logger->warning('User is not authenticated');
        }
        dump($this->container->get('security.token_storage'));
        dump($this->getUser());
        //$reviews = $this->reviewRepository->findAllForList();
        //dd($reviews);
        $reviews = $this->reviewManager->getActiveReviewsForDisplayPaginated(
            $paginator,
            $request->query->getInt('page', 1),
            8
        );
        //dd($reviews);


        return $this->render('Home/index.html.twig', [
            'reviews' => $reviews,
            'user' => $user,
            'isAuthenticated' => $user !== null
        ]);
    }
}
