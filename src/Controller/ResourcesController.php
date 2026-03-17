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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\ResourceUsageService;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

final class ResourcesController extends AbstractController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }
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

    #[Route('/journal/{code}/resources/upload', name: 'app_resources_upload', requirements: ['code' => '[\w\-]+'], methods: ['POST'])]
    public function upload(Request $request, string $code, SluggerInterface $slugger, UploadDirectoryService $dirs): JsonResponse
    {
        // Validate CSRF token for security
        $csrfToken = $request->headers->get('X-CSRF-Token') ?? $request->request->get('_csrf_token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('resources_upload', $csrfToken))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ], 403);
        }

        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('file');
        $overwrite = $request->request->getBoolean('overwrite', false);
        $action = $request->request->get('action'); // 'replace', 'rename', or 'custom'
        $customFileName = $request->request->get('customFileName');

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

        // Get upload directory first
        $uploadDirectory = $dirs->getUploadDirectory($code);

        // Handle custom filename
        if ($action === 'custom' && $customFileName) {
            // Extract filename without extension from custom name
            $customNameParts = pathinfo($customFileName);
            $customBaseName = $customNameParts['filename'];

            // Use custom name but keep original extension
            $safeFilename = (string) $slugger->slug($customBaseName);
            $newFilename = $safeFilename . '.' . $extension;

            // Check if the custom filename already exists
            $destinationPath = $uploadDirectory . '/' . $newFilename;

            if (file_exists($destinationPath)) {
                return new JsonResponse([
                    'success' => false,
                    'conflict' => true,
                    'message' => 'Custom filename already exists',
                    'existingFile' => $newFilename,
                    'originalName' => $uploadedFile->getClientOriginalName(),
                    'isCustomName' => true
                ], 409);
            }
        } else {
            // Generate safe filename from original
            $safeFilename = (string) $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '.' . $extension;
            // Set destination path for non-custom filenames
            $destinationPath = $uploadDirectory . '/' . $newFilename;
        }

        // Ensure the directory is writable
        if (!is_writable($uploadDirectory)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Upload directory is not writable: ' . $uploadDirectory
            ], 500);
        }

        if (file_exists($destinationPath)) {
            // If no action is specified, ask the user what to do
            if (!$action && !$overwrite) {
                return new JsonResponse([
                    'success' => false,
                    'conflict' => true,
                    'message' => 'File already exists',
                    'existingFile' => $newFilename,
                    'originalName' => $uploadedFile->getClientOriginalName()
                ], 409); // 409 Conflict status code
            }
            
            // Handle user's choice
            if ($action === 'rename') {
                // Generate unique filename
                $counter = 1;
                do {
                    $newFilename = $safeFilename . '_' . $counter . '.' . $extension;
                    $destinationPath = $uploadDirectory . '/' . $newFilename;
                    $counter++;
                } while (file_exists($destinationPath));
            } elseif ($action === 'replace' || $overwrite) {
                // Delete the existing file before uploading the new one
                try {
                    unlink($destinationPath);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Failed to delete existing file: ' . $e->getMessage()
                    ], 500);
                }
            } elseif ($action === 'custom') {
                // Custom filename conflict should have been handled earlier
                // If we reach here, something went wrong
                return new JsonResponse([
                    'success' => false,
                    'conflict' => true,
                    'message' => 'Custom filename already exists',
                    'existingFile' => $newFilename,
                    'originalName' => $uploadedFile->getClientOriginalName(),
                    'isCustomName' => true
                ], 409);
            }
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
    public function delete(Request $request, string $code, string $filename, UploadDirectoryService $dirs): JsonResponse
    {
        // Validate CSRF token for security
        $csrfToken = $request->headers->get('X-CSRF-Token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('resources_delete', $csrfToken))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ], 403);
        }

        $uploadDirectory = $dirs->getUploadDirectory($code);
        $filePath = $uploadDirectory . '/' . $filename;

        // Security: Prevent path traversal attacks by validating the resolved path
        $realFilePath = realpath($filePath);
        $realUploadDir = realpath($uploadDirectory);

        if ($realFilePath === false || $realUploadDir === false) {
            return new JsonResponse([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Ensure the file is within the allowed upload directory
        if (strpos($realFilePath, $realUploadDir) !== 0) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        if (!file_exists($realFilePath)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        try {
            unlink($realFilePath);

            return new JsonResponse([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to delete file'
            ], 500);
        }
    }

    #[Route('/journal/{code}/resources/list', name: 'app_resources_list', requirements: ['code' => '[\w\-]+'], methods: ['GET'])]
    public function list(string $code, UploadDirectoryService $dirs): JsonResponse
    {
        $files = $this->getFilesForJournal($code, $dirs);
        
        return new JsonResponse([
            'success' => true,
            'files' => $files
        ]);
    }

    #[Route('/journal/{code}/resources/check-exists', name: 'app_resources_check_exists', requirements: ['code' => '[\w\-]+'], methods: ['POST'])]
    public function checkExists(Request $request, string $code, UploadDirectoryService $dirs, SluggerInterface $slugger): JsonResponse
    {
        $filename = $request->request->get('filename');
        
        if (!$filename) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Filename is required'
            ], 400);
        }

        // Extract filename without extension from input
        $filenameParts = pathinfo($filename);
        $baseName = $filenameParts['filename'];
        $extension = isset($filenameParts['extension']) ? $filenameParts['extension'] : '';
        
        // Apply the same slugging logic as in upload
        $safeFilename = (string) $slugger->slug($baseName);
        $finalFilename = $extension ? $safeFilename . '.' . $extension : $safeFilename;
        
        $uploadDirectory = $dirs->getUploadDirectory($code);
        $filePath = $uploadDirectory . '/' . $finalFilename;
        
        return new JsonResponse([
            'success' => true,
            'exists' => file_exists($filePath),
            'filename' => $finalFilename
        ]);
    }

    #[Route('/journal/{code}/resources/{filename}/check-usage', name: 'app_resources_check_usage', requirements: ['code' => '[\w\-]+'], methods: ['GET'])]
    public function checkUsage(string $code, string $filename, ResourceUsageService $usageService): JsonResponse
    {
        try {
            $usageSummary = $usageService->getResourceUsageSummary($filename, $code);

            return new JsonResponse([
                'success' => true,
                'inUse' => $usageSummary['inUse'],
                'pageCount' => $usageSummary['pageCount'],
                'pages' => $usageSummary['pages']
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error checking resource usage: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{code}/resources/{filename}', name: 'app_resources_serve', requirements: ['code' => '[\w\-]+', 'filename' => '.+'])]
    public function serve(string $code, string $filename, UploadDirectoryService $dirs): Response
    {
        $uploadDirectory = $dirs->getUploadDirectory($code);
        $filePath = $uploadDirectory . '/' . $filename;

        // Security: Prevent path traversal attacks by validating the resolved path
        $realFilePath = realpath($filePath);
        $realUploadDir = realpath($uploadDirectory);

        if ($realFilePath === false || $realUploadDir === false) {
            throw $this->createNotFoundException('File not found');
        }

        // Ensure the file is within the allowed upload directory
        if (strpos($realFilePath, $realUploadDir) !== 0) {
            throw $this->createAccessDeniedException('Access denied');
        }

        if (!file_exists($realFilePath)) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new BinaryFileResponse($realFilePath);

        // Use basename to get only the filename, preventing header injection
        $safeFilename = basename($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $safeFilename);

        return $response;
    }

    private function getFilesForJournal(string $code, UploadDirectoryService $dirs): array
    {
        $directory = $dirs->getUploadDirectory($code);
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
                    'url' => $this->generatePublicUrl($code, $fileInfo->getFilename())
                ];
            }
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $files;
    }


    private function generatePublicUrl(string $code, string $filename): string
    {
        return "/{$code}/resources/{$filename}";
    }
}