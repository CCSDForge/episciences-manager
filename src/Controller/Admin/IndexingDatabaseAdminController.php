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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/indexing-databases')]
class IndexingDatabaseAdminController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface     $entityManager,
        private readonly IndexingDatabaseRepository $repository,
        private readonly IndexingDatabaseService    $indexingDatabaseService,
    )
    {
    }

    #[Route('', name: 'app_admin_indexing_database_index', methods: ['GET'])]
    public function index(Request $request,  PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_LIST);

        $statusFilter = $request->query->get('status');

        if ($statusFilter === IndexingDatabaseStatus::PENDING->value) {
            $qb = $this->repository->queryPending();
        } else {
            $qb = $this->repository->queryAll();

            if ($statusFilter && in_array($statusFilter,
                    IndexingDatabaseStatus::values(), true)) {
                $qb->andWhere('idb.status = :status')
                    ->setParameter('status',
                        IndexingDatabaseStatus::from($statusFilter));
            }
        }

        // PAGINATION
        $pagination = $paginator->paginate(
            $qb,                                        // QueryBuilder
            $request->query->getInt('page', 1),         // page courante
            8                                          // items par page
        );

        $allCount = $this->repository->count([]);
        $validatedCount = $this->repository->count(['status' => IndexingDatabaseStatus::VALIDATED]);
        $pendingCount = $this->repository->count(['status' => IndexingDatabaseStatus::PENDING]);
        $rejectedCount = $this->repository->count(['status' => IndexingDatabaseStatus::REJECTED]);

        return $this->render('indexingDatabase/admin/index.html.twig', [
            'pagination' => $pagination,
            'allCount' => $allCount,
            'validatedCount' => $validatedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'currentFilter' => $statusFilter,
        ]);
    }

    #[Route('/{id}/show', name: 'app_admin_indexing_database_show', methods: ['GET'])]
    public function show(IndexingDatabase $database): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_LIST);

        return $this->render('indexingDatabase/admin/show.html.twig', [
            'database' => $database,
        ]);
    }
    #[Route('/check-url', name:'app_admin_indexing_database_check_url', methods: ['GET'])]
    public function checkUrl(Request $request): JsonResponse
    {
        $url = $request->query->get('url');

        if (empty($url)) {
            return new JsonResponse(['exists' => false]);
        }

        $exists = $this->repository->findOneBy(['url' => $url]) !==
            null;

        return new JsonResponse(['exists' => $exists]);
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

        $this->indexingDatabaseService->delete($database);

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

        try {
            if ($isNew) {
                /** @var User $user */
                $user = $this->getUser();
                $this->indexingDatabaseService->create(
                    name: $name,
                    url: $url ?: null,
                    logo: $request->files->get('logo'),
                    createdBy: $user,
                    status: IndexingDatabaseStatus::VALIDATED
                );
                $this->addFlash('success', 'indexingDatabase.flash.created');
            } else {
                $this->indexingDatabaseService->update(
                    database: $database,
                    name: $name,
                    url: $url ?: null,
                    newLogo: $request->files->get('logo'),
                    removeLogo: $request->request->get('remove_logo') === '1'
                );
                $this->addFlash('success', 'indexingDatabase.flash.updated');
            }
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
            return $isNew
                ? $this->redirectToRoute('app_admin_indexing_database_create')
                : $this->redirectToRoute('app_admin_indexing_database_edit', ['id' => $database->getId()]);
        }

        return $this->redirectToRoute('app_admin_indexing_database_index');
    }
}
