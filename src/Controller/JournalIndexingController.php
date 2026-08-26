<?php
namespace App\Controller;

use App\Entity\IndexingDatabase;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;
use App\Repository\IndexingDatabaseRepository;
use App\Repository\ReviewRepository;
use App\Security\Voter\IndexingDatabaseVoter;
use App\Service\ReviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/journal/{code}/indexing')]
class JournalIndexingController extends AbstractController
{
    private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
    private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
    private const UPLOAD_DIR = 'data/indexing-databases';

    public function __construct(
        private readonly EntityManagerInterface     $entityManager,
        private readonly IndexingDatabaseRepository $repository,
        private readonly ReviewRepository           $reviewRepository,
        private readonly ReviewManager              $reviewManager,
        private readonly SluggerInterface           $slugger,
    )
    {
    }

    #[Route('', name: 'app_journal_indexing', methods: ['GET'])]
    public function index(string $code): Response
    {
        $reviewData = $this->reviewManager->getReviewByCode($code);
        //var_dump($reviewData);
        if (!$reviewData) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ASSOCIATE, $reviewData);

        // Get all validated databases
        $databases = $this->repository->findAllValidated();

        // Get IDs of databases already associated with this review
        $associatedDatabases = $this->repository->findByReview($reviewData['rvid']);
        $associatedIds = array_map(
            fn(IndexingDatabase $db) => $db->getId(),
            $associatedDatabases
        );

        // DEBUG - to remove
        //dump('rvid: ' . $reviewData['rvid']);
        //dump('associatedIds from Doctrine: ', $associatedIds);

        // Test SQL direct
        //$conn = $this->entityManager->getConnection();
        //$sql = 'SELECT * FROM REVIEW_INDEXING_DATABASE WHERE rvid = :rvid';
       // $sqlResult = $conn->executeQuery($sql, ['rvid' => $reviewData['rvid']])->fetchAllAssociative();
        //dump('SQL direct result: ', $sqlResult);

        // Check if user can propose new databases
        $canPropose = $this->isGranted(IndexingDatabaseVoter::PROPOSE, $reviewData);

        // Get all pending proposals (visible to all roles with ASSOCIATE permission)
        $pendingProposals = $this->repository->findPending();

        // Get all rejected proposals
        $rejectedProposals = $this->repository->findRejected();

        return $this->render('indexingDatabase/journal/index.html.twig', [
            'code' => $code,
            'review' => $reviewData,
            'indexingDatabases' => $databases,
            'associatedIds' => $associatedIds,
            'canPropose' => $canPropose,
            'pendingProposals' => $pendingProposals,
            'rejectedProposals' => $rejectedProposals,
        ]);
    }

    #[Route('/toggle/{id}', name: 'app_journal_indexing_toggle', methods: ['POST'])]
    public function toggle(string $code, int $id, Request $request): JsonResponse
    {
        $reviewData = $this->reviewManager->getReviewByCode($code);
        if (!$reviewData) {
            return new JsonResponse(['success' => false, 'error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ASSOCIATE, $reviewData);

        $review = $this->reviewRepository->find($reviewData['rvid']);
        if (!$review) {
            return new JsonResponse(['success' => false, 'error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $database = $this->repository->find($id);
        if (!$database || !$database->isValidated()) {
            return new JsonResponse(['success' => false, 'error' => 'Database not found'], Response::HTTP_NOT_FOUND);
        }

        // Validate CSRF token
        $token = $request->request->get('_token') ?? $request->headers->get('X-CSRF-Token');
        if (!$this->isCsrfTokenValid('indexing-toggle-' . $id, $token)) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        // Toggle association
        $isAssociated = $database->getReviews()->contains($review);
        if ($isAssociated) {
            $database->removeReview($review);
            $action = 'dissociated';
        } else {
            $database->addReview($review);
            $action = 'associated';
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'action' => $action,
            'databaseName' => $database->getName(),
        ]);
    }

    #[Route('/propose', name: 'app_journal_indexing_propose', methods: ['POST'])]
    public function propose(string $code, Request $request): Response
    {
        $reviewData = $this->reviewManager->getReviewByCode($code);
        if (!$reviewData) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'error' => 'Review not found'], Response::HTTP_NOT_FOUND);
            }
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::PROPOSE, $reviewData);

        // Validate CSRF token
        $token = $request->request->get('_token') ?? $request->headers->get('X-CSRF-Token');
        if (!$this->isCsrfTokenValid('indexing-propose', $token)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
            }
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_journal_indexing', ['code' => $code]);
        }

        $name = trim($request->request->get('name', ''));
        $url = trim($request->request->get('url', ''));

        if (empty($name)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
            }
            $this->addFlash('error', 'Name is required');
            return $this->redirectToRoute('app_journal_indexing', ['code' => $code]);
        }

        // Handle logo upload
        $logoPath = null;
        $logoFile = $request->files->get('logo');
        if ($logoFile instanceof UploadedFile) {
            $extension = strtolower($logoFile->getClientOriginalExtension());

            if (!in_array($extension, self::ALLOWED_LOGO_EXTENSIONS, true)) {
                $error = 'Invalid file type. Allowed: ' . implode(', ', self::ALLOWED_LOGO_EXTENSIONS);
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => false, 'error' => $error], Response::HTTP_BAD_REQUEST);
                }
                $this->addFlash('error', $error);
                return $this->redirectToRoute('app_journal_indexing', ['code' => $code]);
            }

            if ($logoFile->getSize() > self::MAX_LOGO_SIZE) {
                $error = 'File too large. Maximum size: 2MB';
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => false, 'error' => $error], Response::HTTP_BAD_REQUEST);
                }
                $this->addFlash('error', $error);
                return $this->redirectToRoute('app_journal_indexing', ['code' => $code]);
            }

            $safeFilename = $this->slugger->slug(pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME));
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

            $uploadDir = $this->getParameter('kernel.project_dir') . '/' . self::UPLOAD_DIR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $logoFile->move($uploadDir, $newFilename);
            // Store only the filename (not the full path)
            $logoPath = $newFilename;
        }

        /** @var User $user */
        $user = $this->getUser();

        $database = new IndexingDatabase();
        $database->setName($name);
        $database->setUrl($url ?: null);
        $database->setLogo($logoPath);
        $database->setStatus(IndexingDatabaseStatus::PENDING);
        $database->setCreatedBy($user);

        $this->entityManager->persist($database);
        $this->entityManager->flush();

        // AJAX response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'database' => [
                    'id' => $database->getId(),
                    'name' => $database->getName(),
                    'url' => $database->getUrl(),
                    'logo' => $database->getLogo(),
                ],
            ]);
        }

        // Classic response (form submission)
        $this->addFlash('success', 'indexingDatabase.flash.proposed');
        return $this->redirectToRoute('app_journal_indexing', ['code' => $code]);
    }
}
