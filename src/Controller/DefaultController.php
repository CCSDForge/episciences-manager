<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET'])]
    public function login(Request $request): RedirectResponse
    {
        // Retrieve the base return URL from parameters (defined in .env)
        $baseUrl = rtrim($this->getParameter('cas_service_base_url'), '/');

        // Prepare the full return URL including the /force path
        $target = urlencode($baseUrl . '/user');

        // Construct the CAS login URL with the service parameter
        $url = 'https://' . 'cas-preprod.ccsd.cnrs.fr'
            . '/cas/login?service=' . $target;

        // Redirect the user to the CAS login page
        return $this->redirect($url);
    }


    #[Route('/logout', name:'logout', methods: ['GET'])]
    public function logout(Request $request) {
        if (($this->getParameter('cas_logout_target') !== null) && (!empty($this->getParameter('cas_logout_target')))) {
            \phpCAS::logoutWithRedirectService($this->getParameter('cas_logout_target'));
        } else {
            \phpCAS::logout();
        }
    }

    #[Route('/force', name:'force', methods: ['GET'])]
    public function force(Request $request) {

        if ($this->getParameter("cas_gateway")) {
            if (!isset($_SESSION)) {
                session_start();
            }

            session_destroy();
        }

        return $this->redirect($this->generateUrl('index'));
    }

    #[Route('/', name:'index', methods: ['GET'])]
    public function index(Request $request) : Response
    {
        // dump($this->container->get('security.token_storage'));
        // dump($this->getUser());

        return $this->render('Home/index.html.twig', []);
    }
}
