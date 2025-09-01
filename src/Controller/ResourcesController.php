<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\UploadDirectoryService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ResourcesController extends AbstractController
{
    private const ALLOWED_EXTENSIONS = [
        'png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff', 'zip', 'gz', '7z', 'rar', 
        'tar', 'bz', 'bz2', 'rtf', 'doc', 'eps', 'ps', 'dvi', 'docx', 'pdf', 
        'txt', 'md', 'odt', 'ods', 'xls', 'xlsx', 'tex', 'bbl', 'bbx', 'bib', 
        'bst', 'cbx', 'cls', 'def', 'dbx', 'dtx', 'lbx', 'sty'
    ];

    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    #[Route('/journal/{code}/resources', name: 'app_resources_manager', requirements: ['code' => '[\w\-]+'])]
    public function index(string $code, UploadDirectoryService $dirs): Response
    {
        $files = $this->getFilesForJournal($code, $dirs);

        return $this->render('resources/index.html.twig', [
            'files' => $files,
            'journal_code' => $code,
        ]);
    }

    #[Route('/journal/{code}/resources/upload', name: 'app_resources_upload', methods: ['POST'], requirements: ['code' => '[\w\-]+'])]
    public function upload(Request $request, string $code, SluggerInterface $slugger, UploadDirectoryService $dirs): JsonResponse
    {
        // Debug logging
        error_log('Upload request received for journal: ' . $code);
        error_log('Files in request: ' . print_r($_FILES, true));
        error_log('Post data: ' . print_r($_POST, true));

        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('file');
        $overwrite = $request->request->getBoolean('overwrite', false);

        if (!$uploadedFile) {
            error_log('No file uploaded in request');
            return new JsonResponse([
                'success' => false, 
                'message' => 'No file uploaded'
            ], 400);
        }

        // Validate file size
        if ($uploadedFile->getSize() > self::MAX_FILE_SIZE) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'File too large. Maximum size: ' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB'
            ], 400);
        }

        // Validate file extension
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($uploadedFile->getClientOriginalExtension());

        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'File type not allowed. Allowed types: ' . implode(', ', self::ALLOWED_EXTENSIONS)
            ], 400);
        }

        // Generate safe filename
        $safeFilename = (string) $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '.' . $extension;

        // Check if file already exists and handle accordingly
        $uploadDirectory = $dirs->getUploadDirectory($code);
        $destinationPath = $uploadDirectory . '/' . $newFilename;

        if (file_exists($destinationPath) && !$overwrite) {
            // Generate unique filename
            $counter = 1;
            do {
                $newFilename = $safeFilename . '_' . $counter . '.' . $extension;
                $destinationPath = $uploadDirectory . '/' . $newFilename;
                $counter++;
            } while (file_exists($destinationPath));
        }

        try {
            $uploadedFile->move($uploadDirectory, $newFilename);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'File uploaded successfully',
                'filename' => $newFilename,
                'url' => $this->generatePublicUrl($code, $newFilename)
            ]);

        } catch (FileException $e) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/journal/{code}/resources/{filename}/delete', name: 'app_resources_delete', methods: ['DELETE'], requirements: ['code' => '[\w\-]+'])]
    public function delete(string $code, string $filename, UploadDirectoryService $dirs): JsonResponse
    {
        $filePath = $dirs->getUploadDirectory($code) . '/' . $filename;

        if (!file_exists($filePath)) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'File not found'
            ], 404);
        }

        try {
            unlink($filePath);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/journal/{code}/resources/list', name: 'app_resources_list', methods: ['GET'], requirements: ['code' => '[\w\-]+'])]
    public function list(string $code, UploadDirectoryService $dirs): JsonResponse
    {
        $files = $this->getFilesForJournal($code, $dirs);
        
        return new JsonResponse([
            'success' => true,
            'files' => $files
        ]);
    }

    #[Route('/{code}/resources/{filename}', name: 'app_resources_serve', requirements: ['code' => '[\w\-]+', 'filename' => '.+'])]
    public function serve(string $code, string $filename, UploadDirectoryService $dirs): Response
    {
        $filePath = $dirs->getUploadDirectory($code) . '/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
        
        return $response;
    }

    private function getFilesForJournal(string $journalCode, UploadDirectoryService $dirs): array
    {
        $directory = $dirs->getUploadDirectory($journalCode);
        $files = [];

        if (!is_dir($directory)) {
            return $files;
        }

        $iterator = new \DirectoryIterator($directory);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($fileInfo->isFile()) {
                $files[] = [
                    'name' => $fileInfo->getFilename(),
                    'size' => $fileInfo->getSize(),
                    'modified' => $fileInfo->getMTime(),
                    'url' => $this->generatePublicUrl($journalCode, $fileInfo->getFilename())
                ];
            }
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $files;
    }


    private function generatePublicUrl(string $journalCode, string $filename): string
    {
        return $this->generateUrl('app_resources_serve', [
            'code' => $journalCode,
            'filename' => $filename,
        ]);
    }
}