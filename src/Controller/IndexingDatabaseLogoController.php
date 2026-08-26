<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class IndexingDatabaseLogoController extends AbstractController
{
    private const UPLOAD_DIR = 'data/indexing-databases';

    #[Route('/indexing-databases/logo/{filename}', name: 'app_indexing_database_logo', requirements: ['filename' => '.+'])]
    public function serve(string $filename): Response
    {
        $uploadDir = $this->getParameter('kernel.project_dir') . '/' . self::UPLOAD_DIR;
        $filePath = $uploadDir . '/' . $filename;

        // Security: Prevent path traversal attacks
        $realFilePath = realpath($filePath);
        $realUploadDir = realpath($uploadDir);

        if ($realFilePath === false || $realUploadDir === false) {
            throw $this->createNotFoundException('Logo not found');
        }

        // Ensure the file is within the allowed upload directory
        if (!str_starts_with($realFilePath, $realUploadDir)) {
            throw $this->createAccessDeniedException('Access denied');
        }

        if (!file_exists($realFilePath)) {
            throw $this->createNotFoundException('Logo not found');
        }

        $response = new BinaryFileResponse($realFilePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($filename));

        return $response;
    }
}