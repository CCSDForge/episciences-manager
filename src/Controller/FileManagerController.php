<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FileManagerController extends AbstractController
{
    private const ALLOWED_EXTENSIONS = [
        'png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff', 'zip', 'gz', '7z', 'rar', 
        'tar', 'bz', 'bz2', 'rtf', 'doc', 'eps', 'ps', 'dvi', 'docx', 'pdf', 
        'txt', 'md', 'odt', 'ods', 'xls', 'xlsx', 'tex', 'bbl', 'bbx', 'bib', 
        'bst', 'cbx', 'cls', 'def', 'dbx', 'dtx', 'lbx', 'sty'
    ];

    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    #[Route('/journal/{code}/files', name: 'app_file_manager', requirements: ['code' => '[\w\-]+'])]
    public function index(string $code): Response
    {
        $files = $this->getFilesForJournal($code);

        return $this->render('file_manager/index.html.twig', [
            'files' => $files,
            'journal_code' => $code,
        ]);
    }

    #[Route('/journal/{code}/files/upload', name: 'app_file_upload', methods: ['POST'], requirements: ['code' => '[\w\-]+'])]
    public function upload(Request $request, string $code, SluggerInterface $slugger): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');
        $overwrite = $request->request->getBoolean('overwrite', false);

        if (!$uploadedFile) {
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
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '.' . $extension;

        // Check if file already exists and handle accordingly
        $uploadDirectory = $this->getUploadDirectory($code);
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

    #[Route('/journal/{code}/files/{filename}/delete', name: 'app_file_delete', methods: ['DELETE'], requirements: ['code' => '[\w\-]+'])]
    public function delete(string $code, string $filename): JsonResponse
    {
        $filePath = $this->getUploadDirectory($code) . '/' . $filename;

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

    #[Route('/journal/{code}/files/list', name: 'app_file_list', methods: ['GET'], requirements: ['code' => '[\w\-]+'])]
    public function list(string $code): JsonResponse
    {
        $files = $this->getFilesForJournal($code);
        
        return new JsonResponse([
            'success' => true,
            'files' => $files
        ]);
    }

    private function getUploadDirectory(string $journalCode): string
    {
        $directory = $this->getParameter('kernel.project_dir') . '/data/' . $journalCode . '/public';
        
        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        return $directory;
    }

    private function getFilesForJournal(string $journalCode): array
    {
        $directory = $this->getUploadDirectory($journalCode);
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
        return '/' . $journalCode . '/resources/' . $filename;
    }
}