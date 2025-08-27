<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicFileController extends AbstractController
{
    #[Route('/{code}/resources/{filename}', name: 'app_public_file', requirements: ['code' => '[\w\-]+'], methods: ['GET'])]
    public function servePublicFile(string $code, string $filename): Response
    {
        $directory = $this->getParameter('kernel.project_dir') . '/data/' . $code . '/public';
        $filePath = $directory . '/' . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        return $this->file($filePath);
    }
}