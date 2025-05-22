<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_profile')]
    public function index(LoggerInterface $logger): Response
    {
        $token = $this->container->get('security.token_storage')->getToken();
        if (!$token) {
            $logger->error("Token de sécurité non trouvé.");
            return new Response("Erreur : Token non trouvé", 403);
        }

        // Récupération des attributs du token CAS (dont le UID)
        $attributes = $token->getAttributes();

        //dump($attributes);

        $logger->info('Attributs utilisateur CAS', $attributes);

        // Passer tous les attributs au template
        return $this->render('User/index.html.twig', [
            'user_info' => $attributes
        ]);
    }
}
