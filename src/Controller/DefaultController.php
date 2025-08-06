<?php

namespace App\Controller;

use App\Service\ReviewManager;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $secureBaseUrl = $this->loadHttpsOrHttp($baseUrl);

        // Prepare the full return URL including the /force path with locale
        $locale = $request->getLocale();
        $target = urlencode($secureBaseUrl . '/' . $locale . '/force');

        // Construct the CAS login URL with the service parameter
        $url = 'https://' . 'cas-preprod.ccsd.cnrs.fr'
            . '/cas/login?service=' . $target;

        $secureCasUrl = $this->loadHttpsOrHttp($url);
        $logger->info('Redirecting to CAS login URL', ['url' => $secureCasUrl]);
        // Redirect the user to the CAS login page
        return $this->redirect($url);
    }

    #[Route('/logout', name:'logout', methods: ['GET'])]
    public function logout(Request $request, LoggerInterface $logger, TranslatorInterface $translator) {
        $logger->info('Logout action triggered');

        // Nettoyer la session Symfony
        $session = $request->getSession();
        if ($session->isStarted()) {
            $session->clear();
            $session->invalidate();
            $logger->info('Symfony session completely cleared');
        }

        // Nettoyer le token de sécurité
        if ($this->container->has('security.token_storage')) {
            $this->container->get('security.token_storage')->setToken(null);
        }

        // Créer l'URL de retour vers la page d'accueil avec le paramètre logout
        $homeUrl = $this->generateUrl('app_home', ['logout' => 'success'], UrlGeneratorInterface::ABSOLUTE_URL);

        // Construire l'URL de déconnexion CAS avec le paramètre service
        $casLogoutUrl = 'https://cas-preprod.ccsd.cnrs.fr/cas/logout?service=' . urlencode($homeUrl);

        $logger->info('Redirecting to CAS logout', [
            'cas_url' => $casLogoutUrl,
            'service_url' => $homeUrl
        ]);

        $this->addFlash('logout_success', $translator->trans('flash.logout_success'));

        // Redirection vers CAS avec service parameter
        return $this->redirect($casLogoutUrl);
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

        //return $this->redirectToRoute('user_profile');
        return $this->redirectToRoute('app_home');

    }

    /**
     * @param string $url
     * @return string
     */
    private function loadHttpsOrHttp(string $url): string
    {
        try {
            $forceHttps = $this->getParameter('force_https');
        } catch (\Exception $e) {
            // Valeur par défaut : forcer HTTPS en production
            $forceHttps = $this->getParameter('kernel.environment') === 'prod';
        }

        if ($forceHttps === true) {
            if (preg_match("/^(http:\/\/)/", $url)) {
                return str_replace('http', 'https', $url);
            }

            if (preg_match("/^(https:\/\/)/", $url)) {
                return $url;
            }
        }
        return $url;
    }

    #[Route('/', name:'app_home', methods: ['GET'])]
    public function index(Request $request, LoggerInterface $logger,PaginatorInterface $paginator) : Response
    {
        $logger->info('Home page accessed');
        $user = $this->getUser();
        if ($user) {
            $logger->info('User is authenticated', ['user' => $user->getUserIdentifier()]);
        } else {
            $logger->warning('User is not authenticated');
        }
        //dd($user);
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

        $showLogoutMessage = $request->query->get('logout') === 'success';

        return $this->render('home/index.html.twig', [
            'logout_success' => $showLogoutMessage,
            'reviews' => $reviews,
            'user' => $user,
            'isAuthenticated' => $user !== null
        ]);
    }
}
