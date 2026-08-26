<?php

namespace App\Controller\Admin;

use App\Entity\IndexingDatabase;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;
use App\Repository\IndexingDatabaseRepository;
use App\Security\Voter\IndexingDatabaseVoter;
use App\Service\IndexingDatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/indexing-databases')]
class IndexingDatabaseAdminController extends AbstractController
{
    private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
    private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
    private const UPLOAD_DIR = 'data/indexing-databases';

    public function __construct(
        private readonly EntityManagerInterface     $entityManager,
        private readonly IndexingDatabaseRepository $repository,
        private readonly SluggerInterface           $slugger,
        private readonly IndexingDatabaseService    $indexingDatabaseService,
    )
    {
    }

    #[Route('', name: 'app_admin_indexing_database_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_LIST);

        $statusFilter = $request->query->get('status');

        $qb = $this->repository->queryAll();

        if ($statusFilter && in_array($statusFilter, IndexingDatabaseStatus::values(), true)) {
            $qb->andWhere('idb.status = :status')
                ->setParameter('status', IndexingDatabaseStatus::from($statusFilter));
        }

        $databases = $qb->getQuery()->getResult();
        $allCount = $this->repository->count([]);
        $validatedCount = $this->repository->count(['status' => IndexingDatabaseStatus::VALIDATED]);
        $pendingCount = $this->repository->count(['status' => IndexingDatabaseStatus::PENDING]);
        $rejectedCount = $this->repository->count(['status' => IndexingDatabaseStatus::REJECTED]);

        return $this->render('indexingDatabase/admin/index.html.twig', [
            'indexingDatabases' => $databases,
            'allCount' => $allCount,
            'validatedCount' => $validatedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'currentFilter' => $statusFilter,
        ]);
    }

    #[Route('/create', name: 'app_admin_indexing_database_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_CREATE);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('indexing-database-create', $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token');
                return $this->redirectToRoute('app_admin_indexing_database_create');
            }

            try {
                $this->indexingDatabaseService->create(
                    name: trim($request->request->get('name', '')),
                    url: trim($request->request->get('url', '')) ?: null,
                    logo: $request->files->get('logo'),
                    createdBy: $this->getUser(),
                    status: IndexingDatabaseStatus::VALIDATED
                );
                $this->addFlash('success', 'indexingDatabase.flash.created');
                return $this->redirectToRoute('app_admin_indexing_database_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_admin_indexing_database_create');
            }
        }

        return $this->render('indexingDatabase/admin/form.html.twig');

    }

    #[Route('/{id}/edit', name: 'app_admin_indexing_database_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndexingDatabase $database): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_EDIT, $database);

        if ($request->isMethod('POST')) {
            return $this->handleCreateOrEdit($request, $database);
        }

        return $this->render('indexingDatabase/admin/form.html.twig', [
            'indexingDatabase' => $database,
        ]);
    }

    #[Route('/{id}/validate', name: 'app_admin_indexing_database_validate', methods: ['POST'])]
    public function validate(Request $request, IndexingDatabase $database): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_VALIDATE, $database);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('indexing-database-validate-' . $database->getId(), $token)) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_admin_indexing_database_index');
        }

        $action = $request->request->get('action');

        if ($action === 'validate') {
            $database->setStatus(IndexingDatabaseStatus::VALIDATED);
            $this->addFlash('success', 'indexingDatabase.flash.validated');
        } elseif ($action === 'reject') {
            $database->setStatus(IndexingDatabaseStatus::REJECTED);
            $this->addFlash('success', 'indexingDatabase.flash.rejected');
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_indexing_database_index');
    }

    #[Route('/{id}/delete', name: 'app_admin_indexing_database_delete', methods: ['POST'])]
    public function delete(Request $request, IndexingDatabase $database): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_DELETE, $database);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('indexing-database-delete-' . $database->getId(), $token)) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_admin_indexing_database_index');
        }

        // Delete logo file if exists
        if ($database->getLogo()) {
            $logoPath = $this->getParameter('kernel.project_dir') . '/' . self::UPLOAD_DIR . '/' . $database->getLogo();
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        $this->entityManager->remove($database);
        $this->entityManager->flush();

        $this->addFlash('success', 'indexingDatabase.flash.deleted');

        return $this->redirectToRoute('app_admin_indexing_database_index');
    }

    private function handleCreateOrEdit(Request $request, IndexingDatabase $database): Response
    {
        $token = $request->request->get('_token');
        $isNew = $database->getId() === null;
        $csrfId = $isNew ? 'indexing-database-create' : 'indexing-database-edit-' . $database->getId();

        if (!$this->isCsrfTokenValid($csrfId, $token)) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_admin_indexing_database_index');
        }

        $name = trim($request->request->get('name', ''));
        $url = trim($request->request->get('url', ''));

        if ($name === '') {
            $this->addFlash('error', 'Name is required');
            return $isNew
                ? $this->redirectToRoute('app_admin_indexing_database_create')
                : $this->redirectToRoute('app_admin_indexing_database_edit', ['id' => $database->getId()]);
        }

        $database->setName($name);
        $database->setUrl($url ?: null);

        // Handle logo upload
        $logoFile = $request->files->get('logo');
        if ($logoFile) {
            $logoPath = $this->handleLogoUpload($logoFile, $database);
            if ($logoPath === false) {
                return $isNew
                    ? $this->redirectToRoute('app_admin_indexing_database_create')
                    : $this->redirectToRoute('app_admin_indexing_database_edit', ['id' => $database->getId()]);
            }
            if ($logoPath !== null) {
                $database->setLogo($logoPath);
            }
        }

        // Handle logo removal
        if ($request->request->get('remove_logo') === '1' && $database->getLogo()) {
            $oldLogoPath = $this->getParameter('kernel.project_dir') . '/' . self::UPLOAD_DIR . '/' . $database->getLogo();
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
            $database->setLogo(null);
        }

        if ($isNew) {
            /** @var User $user */
            $user = $this->getUser();
            $database->setCreatedBy($user);
            $database->setStatus(IndexingDatabaseStatus::VALIDATED);
            $this->entityManager->persist($database);
            $this->addFlash('success', 'indexingDatabase.flash.created');
        } else {
            $database->setUpdatedAt(new \DateTime());
            $this->addFlash('success', 'indexingDatabase.flash.updated');
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_indexing_database_index');
    }

    /**
     * @return string|false|null Path on success, false on error, null if no file
     */
    private function handleLogoUpload(mixed $uploadedFile, IndexingDatabase $database): string|false|null
    {
        if (!$uploadedFile instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            return null;
        }

        if (!$uploadedFile->isValid()) {
            $this->addFlash('error', 'Logo upload failed');
            return false;
        }

        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_LOGO_EXTENSIONS, true)) {
            $this->addFlash('error', 'Invalid logo format. Allowed: ' . implode(', ', self::ALLOWED_LOGO_EXTENSIONS));
            return false;
        }

        if ($uploadedFile->getSize() > self::MAX_LOGO_SIZE) {
            $this->addFlash('error', 'Logo file too large. Maximum size: 2MB');
            return false;
        }

        // Delete old logo if exists
        if ($database->getLogo()) {
            $oldLogoPath = $this->getParameter('kernel.project_dir') . '/' . self::UPLOAD_DIR . '/' . $database->getLogo();
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
        }

        $slug = $this->slugger->slug($database->getName() ?: 'database')->lower();
        $newFilename = $slug . '-' . uniqid() . '.' . $extension;

        $uploadDir = $this->getParameter('kernel.project_dir') . '/' . self::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadedFile->move($uploadDir, $newFilename);

        // Return only the filename (not the full path)
        return $newFilename;
    }
}
