# Documentation : Gestion des Bases d'Indexation

## Vue d'ensemble

Cette fonctionnalité permet aux revues de valoriser leur référencement dans les bases de données et annuaires scientifiques (DOAJ, AOJ, DDH, etc.).

### Objectifs

- Référentiel centralisé et modéré de bases d'indexation
- Modération exclusive par les comptes `epiadmin`
- Possibilité pour les revues de proposer de nouvelles bases
- Interface simple (toggles) pour indiquer les référencements

---

## Architecture

### Modèle de données

```
┌─────────────────────┐         ┌─────────────────────────────┐
│       REVIEW        │         │     INDEXING_DATABASE       │
├─────────────────────┤         ├─────────────────────────────┤
│ rvid (PK)           │◄───────►│ id (PK)                     │
│ code                │   M:N   │ name                        │
│ name                │         │ url                         │
│ ...                 │         │ logo                        │
└─────────────────────┘         │ status (enum)               │
                                │ created_at                  │
                                │ updated_at                  │
                                │ created_by (FK → USER.UID)  │
                                └─────────────────────────────┘
                                           │
                                           │
                        ┌──────────────────┴──────────────────┐
                        │     REVIEW_INDEXING_DATABASE        │
                        ├─────────────────────────────────────┤
                        │ INDEXING_DATABASE_ID (FK)           │
                        │ RVID (FK)                           │
                        └─────────────────────────────────────┘
```

### Enum IndexingDatabaseStatus

| Valeur     | Description                                    |
|------------|------------------------------------------------|
| `pending`  | En attente de validation par un epiadmin       |
| `validated`| Validée, visible et associable aux revues      |
| `rejected` | Refusée, non visible                           |

---

## Permissions

### Matrice des droits

| Action                          | epiadmin | administrator | chief_editor | secretary |
|---------------------------------|----------|---------------|--------------|-----------|
| Créer une base (validée)        | ✅       | ❌            | ❌           | ❌        |
| Proposer une base (pending)     | ✅       | ✅            | ❌           | ❌        |
| Valider/Rejeter une proposition | ✅       | ❌            | ❌           | ❌        |
| Modifier une base               | ✅       | ❌            | ❌           | ❌        |
| Supprimer une base              | ✅       | ❌            | ❌           | ❌        |
| Associer/Dissocier une revue    | ✅       | ✅            | ✅           | ✅        |

### Voter Symfony

Le `IndexingDatabaseVoter` gère les permissions suivantes :

```php
// Permissions admin (epiadmin uniquement)
INDEXING_DATABASE_ADMIN_LIST
INDEXING_DATABASE_ADMIN_CREATE
INDEXING_DATABASE_ADMIN_EDIT
INDEXING_DATABASE_ADMIN_DELETE
INDEXING_DATABASE_ADMIN_VALIDATE

// Permissions revue (avec RVID)
INDEXING_DATABASE_PROPOSE     // administrator+
INDEXING_DATABASE_ASSOCIATE   // REVIEW_EDIT roles
```

---

## Interfaces utilisateur

### 1. Page de gestion pour une revue

**URL** : `/{locale}/journal/{code}/indexing`

**Fonctionnalités** :
- Liste des bases validées avec toggles on/off
- Bouton "Proposer une nouvelle base" (modal)
- Section "Vos propositions en attente" (si applicable)

**Accès** : Utilisateurs avec `REVIEW_EDIT` sur la revue

### 2. Interface d'administration

**URL** : `/{locale}/admin/indexing-databases`

**Fonctionnalités** :
- Liste paginée de toutes les bases
- Badges de status (En attente, Validée, Refusée)
- Actions : Créer, Modifier, Valider, Rejeter, Supprimer
- Filtre par status

**Accès** : `epiadmin` uniquement

---

## Routes API

### Routes Administration

| Méthode | Route                                    | Action              |
|---------|------------------------------------------|---------------------|
| GET     | `/admin/indexing-databases`              | Liste               |
| GET/POST| `/admin/indexing-databases/create`       | Création            |
| GET/POST| `/admin/indexing-databases/{id}/edit`    | Édition             |
| POST    | `/admin/indexing-databases/{id}/validate`| Valider/Rejeter     |
| DELETE  | `/admin/indexing-databases/{id}/delete`  | Suppression         |

### Routes Revue

| Méthode | Route                                      | Action              |
|---------|--------------------------------------------|---------------------|
| GET     | `/journal/{code}/indexing`                 | Page de gestion     |
| POST    | `/journal/{code}/indexing/toggle/{id}`     | Toggle association  |
| POST    | `/journal/{code}/indexing/propose`         | Soumettre proposition|

### Routes Assets

| Méthode | Route                                      | Action              |
|---------|--------------------------------------------|---------------------|
| GET     | `/indexing-databases/logo/{filename}`      | Servir le logo      |

---

## Structure des fichiers

```
src/
├── Controller/
│   ├── Admin/
│   │   └── IndexingDatabaseAdminController.php
│   ├── IndexingDatabaseLogoController.php  ← Sert les logos
│   └── JournalIndexingController.php
├── Doctrine/DBAL/Type/
│   └── IndexingDatabaseStatusType.php
├── Entity/
│   ├── IndexingDatabase.php
│   └── Review.php  (modifié)
├── Enum/
│   └── IndexingDatabaseStatus.php
├── Repository/
│   └── IndexingDatabaseRepository.php
└── Security/Voter/
    └── IndexingDatabaseVoter.php

assets/controllers/
├── indexing_database_admin_controller.js
└── indexing_database_toggle_controller.js

templates/indexingDatabase/
├── admin/
│   ├── index.html.twig
│   ├── create.html.twig
│   └── edit.html.twig
└── journal/
    └── index.html.twig

migrations/
└── Version20260728000000.php

data/indexing-databases/
└── (logos uploadés)
```

---

## Upload des logos

### Spécifications

| Paramètre           | Valeur                           |
|---------------------|----------------------------------|
| Extensions acceptées| png, jpg, jpeg, gif, svg, webp   |
| Taille maximale     | 2 Mo                             |
| Répertoire stockage | `data/indexing-databases/`       |
| Nommage fichier     | `{slug}-{uniqid}.{ext}`          |
| Valeur en BDD       | Seulement le nom du fichier      |
| Route pour servir   | `/indexing-databases/logo/{filename}` |

### Architecture

```
data/indexing-databases/
└── doaj-66a1b2c3.png          ← Fichier physique

BDD (colonne logo):
'doaj-66a1b2c3.png'            ← Seulement le nom

Template Twig:
{{ path('app_indexing_database_logo', {filename: db.logo}) }}
→ /indexing-databases/logo/doaj-66a1b2c3.png
```

### Contrôleur pour servir les logos

**Fichier** : `src/Controller/IndexingDatabaseLogoController.php`

```php
#[Route('/indexing-databases/logo/{filename}', name: 'app_indexing_database_logo')]
public function serve(string $filename): Response
{
    // Sert le fichier depuis data/indexing-databases/ avec BinaryFileResponse
}
```

### Validation

```php
private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
private const UPLOAD_DIR = 'data/indexing-databases';
```

---

## Stimulus Controllers

### journal_indexing_controller

**Targets** :
- `toggle` : Checkboxes d'association
- `alert` : Container pour les messages
- `proposeForm` : Formulaire de proposition

**Values** :
- `toggleUrl` : URL template pour toggle
- `proposeUrl` : URL pour soumettre proposition
- `csrfToken` : Token CSRF

**Actions** :
- `toggle` : Envoie requête POST pour associer/dissocier
- `propose` : Soumet le formulaire de proposition

### indexing_database_admin_controller

**Targets** :
- `alert` : Container pour les messages
- `deleteModal` : Modal de confirmation
- `deleteName` : Affichage du nom à supprimer

**Values** :
- `validateUrl` : URL template pour validation
- `deleteUrl` : URL template pour suppression
- `csrfValidate` : Token CSRF validation
- `csrfDelete` : Token CSRF suppression

**Actions** :
- `validate` : Valide ou rejette une proposition
- `openDeleteModal` : Ouvre la modal de confirmation
- `confirmDelete` : Confirme la suppression

---

## Traductions

### Clés principales

```yaml
indexingDatabase:
  admin:
    title: "Bases d'indexation"
    add: "Ajouter une base"
    pending: "en attente"
    deleteConfirm: "Supprimer la base d'indexation"

  journal:
    title: "Référencement & Indexation"
    propose: "Proposer une nouvelle base"
    proposeTitle: "Proposer une nouvelle base d'indexation"
    proposeInfo: "Votre proposition sera examinée par l'équipe d'administration."
    availableDatabases: "Bases d'indexation disponibles"
    indexed: "Référencé"

  status:
    pending: "En attente"
    validated: "Validée"
    rejected: "Refusée"

  flash:
    created: "Base d'indexation créée avec succès"
    updated: "Base d'indexation mise à jour"
    deleted: "Base d'indexation supprimée"
    validated: "Base d'indexation validée"
    rejected: "Base d'indexation refusée"
    proposed: "Votre proposition a été soumise"
    associated: "La revue est maintenant référencée dans %name%"
    dissociated: "La revue n'est plus référencée dans %name%"
```

---

## Tests

### Tests unitaires (Pest)

```php
// tests/Unit/Security/Voter/IndexingDatabaseVoterTest.php
it('allows epiadmin to create databases', function () {
    $user = createUserWithRole('epiadmin', rvid: 1);
    $voter = new IndexingDatabaseVoter();

    expect($voter->vote($token, null, ['INDEXING_DATABASE_ADMIN_CREATE']))
        ->toBe(VoterInterface::ACCESS_GRANTED);
});

it('denies administrator from validating databases', function () {
    $user = createUserWithRole('administrator', rvid: 1);
    $database = new IndexingDatabase();

    expect($voter->vote($token, $database, ['INDEXING_DATABASE_ADMIN_VALIDATE']))
        ->toBe(VoterInterface::ACCESS_DENIED);
});
```

### Tests E2E (Playwright)

```javascript
// tests/e2e/indexing-database.spec.js
test('epiadmin can validate a pending database', async ({ page }) => {
  await loginAsEpiadmin(page);
  await page.goto('/admin/indexing-databases');

  await page.click('[data-action="validate"][data-id="1"]');

  await expect(page.locator('.alert-success')).toBeVisible();
});

test('journal admin can toggle association', async ({ page }) => {
  await loginAsAdmin(page, 'my-journal');
  await page.goto('/journal/my-journal/indexing');

  const toggle = page.locator('[data-id="1"]');
  await toggle.click();

  await expect(page.locator('.alert-success')).toBeVisible();
});
```

---

## Migration

```php
// migrations/Version20260728000000.php
public function up(Schema $schema): void
{
    $this->addSql("CREATE TABLE INDEXING_DATABASE (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(500) DEFAULT NULL,
        logo VARCHAR(500) DEFAULT NULL,
        status ENUM('pending', 'validated', 'rejected') NOT NULL DEFAULT 'pending',
        CREATED_AT DATETIME NOT NULL,
        UPDATED_AT DATETIME NOT NULL,
        CREATED_BY INT UNSIGNED DEFAULT NULL,
        PRIMARY KEY (id),
        INDEX IDX_STATUS (status),
        CONSTRAINT FK_CREATED_BY FOREIGN KEY (CREATED_BY) REFERENCES USER (UID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $this->addSql("CREATE TABLE REVIEW_INDEXING_DATABASE (
        INDEXING_DATABASE_ID INT UNSIGNED NOT NULL,
        RVID INT NOT NULL,
        PRIMARY KEY (INDEXING_DATABASE_ID, RVID),
        CONSTRAINT FK_IDB FOREIGN KEY (INDEXING_DATABASE_ID)
            REFERENCES INDEXING_DATABASE (id) ON DELETE CASCADE,
        CONSTRAINT FK_REVIEW FOREIGN KEY (RVID)
            REFERENCES REVIEW (rvid) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
```

---

## Accès aux interfaces

### 1. Accès Admin dans la Navbar (epiadmin uniquement)

**Fichier** : `templates/partials/navbar.html.twig`

Ajouter après le lien "Journals" (ligne 22) :

```twig
<li class="nav-item">
    <a class="nav-link" href="{{ path('app_journal') }}" id="nav-journals">{{ 'nav.journal' | trans }}</a>
</li>
{% if is_granted('INDEXING_DATABASE_ADMIN_LIST') %}
<li class="nav-item">
    <a class="nav-link" href="{{ path('app_admin_indexing_database_index') }}" id="nav-admin-indexing">
        {{ 'nav.adminIndexingDatabases' | trans }}
    </a>
</li>
{% endif %}
```

### 2. Accès Journal dans le Dashboard

**Fichier** : `templates/review/journalDashboard.html.twig`

Ajouter dans la section des boutons (après Resources, ligne 54) :

```twig
<a href="{{ path('app_resources_manager', {'code': code}) }}" class="btn btn-outline-success btn-lg">
    <i class="fas fa-folder-open me-2"></i>
    {{ 'journalDashboard.resources'|trans }}
</a>

{% if is_granted('INDEXING_DATABASE_ASSOCIATE', review) %}
<a href="{{ path('app_journal_indexing', {'code': code}) }}" class="btn btn-outline-warning btn-lg">
    <i class="fas fa-database me-2"></i>
    {{ 'journalDashboard.indexingDatabases'|trans }}
</a>
{% endif %}
```

### 3. Traductions à ajouter

**Fichier** : `translations/messages.fr.yaml`

```yaml
nav:
  adminIndexingDatabases: "Admin - Bases d'indexation"

journalDashboard:
  indexingDatabases: "Bases d'indexation"
```

**Fichier** : `translations/messages.en.yaml`

```yaml
nav:
  adminIndexingDatabases: "Admin - Indexing Databases"

journalDashboard:
  indexingDatabases: "Indexing Databases"
```

---

## Code des Controllers

### IndexingDatabaseAdminController.php

```php
<?php

namespace App\Controller;

use App\Entity\IndexingDatabase;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;
use App\Repository\IndexingDatabaseRepository;
use App\Security\Voter\IndexingDatabaseVoter;
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
    private const UPLOAD_DIR = 'uploads/indexing-databases';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly IndexingDatabaseRepository $repository,
        private readonly SluggerInterface $slugger,
    ) {
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
        $pendingCount = count($this->repository->findPending());

        return $this->render('indexingDatabase/admin/index.html.twig', [
            'databases' => $databases,
            'pendingCount' => $pendingCount,
            'currentFilter' => $statusFilter,
        ]);
    }

    #[Route('/create', name: 'app_admin_indexing_database_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_CREATE);

        if ($request->isMethod('POST')) {
            return $this->handleCreateOrEdit($request, new IndexingDatabase());
        }

        return $this->render('indexingDatabase/admin/create.html.twig');
    }

    #[Route('/{id}/edit', name: 'app_admin_indexing_database_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndexingDatabase $database): Response
    {
        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ADMIN_EDIT, $database);

        if ($request->isMethod('POST')) {
            return $this->handleCreateOrEdit($request, $database);
        }

        return $this->render('indexingDatabase/admin/edit.html.twig', [
            'database' => $database,
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
            $logoPath = $this->getParameter('kernel.project_dir') . '/public/' . $database->getLogo();
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
            $oldLogoPath = $this->getParameter('kernel.project_dir') . '/public/' . $database->getLogo();
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
            $oldLogoPath = $this->getParameter('kernel.project_dir') . '/public/' . $database->getLogo();
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
        }

        $slug = $this->slugger->slug($database->getName() ?: 'database')->lower();
        $newFilename = $slug . '-' . uniqid() . '.' . $extension;

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/' . self::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadedFile->move($uploadDir, $newFilename);

        return self::UPLOAD_DIR . '/' . $newFilename;
    }
}
```

### JournalIndexingController.php

**Emplacement** : `src/Controller/JournalIndexingController.php`

**Note importante** : Ce contrôleur utilise le pattern standard du projet :
- `$review` est un **array** (pas une entité) via `ReviewManager::getReviewByCode()`
- On utilise `$review['rvid']` pour les requêtes repository
- Pour `toggle()`, on utilise l'entité `IndexingDatabase` (côté propriétaire de la relation)

```php
<?php

namespace App\Controller;

use App\Entity\IndexingDatabase;
use App\Entity\Review;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;
use App\Repository\IndexingDatabaseRepository;
use App\Repository\ReviewRepository;
use App\Security\Voter\IndexingDatabaseVoter;
use App\Service\ReviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/journal/{code}/indexing')]
class JournalIndexingController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly IndexingDatabaseRepository $indexingDbRepository,
        private readonly ReviewRepository $reviewRepository,
        private readonly ReviewManager $reviewManager,
    ) {
    }

    /**
     * Page principale : liste des bases validées avec toggles
     */
    #[Route('', name: 'app_journal_indexing', methods: ['GET'])]
    public function index(string $code): Response
    {
        // Pattern standard : $review est un array
        $review = $this->reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ASSOCIATE, $review);

        // Toutes les bases validées
        $allDatabases = $this->indexingDbRepository->findAllValidated();

        // Bases associées à cette revue (via repository, pas besoin d'entité)
        $associatedDatabases = $this->indexingDbRepository->findByReview($review['rvid']);
        $associatedIds = array_map(fn($db) => $db->getId(), $associatedDatabases);

        // Propositions en attente de cet utilisateur (si administrator)
        $pendingProposals = [];
        if ($this->isGranted(IndexingDatabaseVoter::PROPOSE, $review)) {
            $pendingProposals = $this->indexingDbRepository->findPendingByCreator($this->getUser());
        }

        return $this->render('indexingDatabase/journal/index.html.twig', [
            'review' => $review,
            'code' => $code,
            'databases' => $allDatabases,
            'associatedIds' => $associatedIds,
            'pendingProposals' => $pendingProposals,
            'canPropose' => $this->isGranted(IndexingDatabaseVoter::PROPOSE, $review),
        ]);
    }

    /**
     * Toggle association revue <-> base d'indexation (AJAX)
     */
    #[Route('/toggle/{id}', name: 'app_journal_indexing_toggle', methods: ['POST'])]
    public function toggle(string $code, IndexingDatabase $database, Request $request): JsonResponse
    {
        $review = $this->reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(['success' => false, 'message' => 'Journal not found'], 404);
        }

        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::ASSOCIATE, $review);

        // Vérifier que la base est validée
        if (!$database->isValidated()) {
            return new JsonResponse(['success' => false, 'message' => 'Database not validated'], 400);
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('indexing-toggle-' . $database->getId(), $token)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 400);
        }

        // Récupérer l'entité Review pour manipuler la relation
        $reviewEntity = $this->reviewRepository->find($review['rvid']);

        if ($reviewEntity === null) {
            return new JsonResponse(['success' => false, 'message' => 'Journal not found'], 404);
        }

        // Toggle via IndexingDatabase (côté propriétaire de la relation ManyToMany)
        $isAssociated = $database->getReviews()->contains($reviewEntity);

        if ($isAssociated) {
            $database->removeReview($reviewEntity);
            $message = 'indexingDatabase.flash.dissociated';
            $associated = false;
        } else {
            $database->addReview($reviewEntity);
            $message = 'indexingDatabase.flash.associated';
            $associated = true;
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'associated' => $associated,
            'message' => $message,
            'name' => $database->getName(),
        ]);
    }

    /**
     * Proposer une nouvelle base d'indexation (AJAX)
     */
    #[Route('/propose', name: 'app_journal_indexing_propose', methods: ['POST'])]
    public function propose(string $code, Request $request): JsonResponse
    {
        $review = $this->reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(['success' => false, 'message' => 'Journal not found'], 404);
        }

        $this->denyAccessUnlessGranted(IndexingDatabaseVoter::PROPOSE, $review);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('indexing-propose', $token)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 400);
        }

        $name = trim($request->request->get('name', ''));
        $url = trim($request->request->get('url', ''));

        if ($name === '') {
            return new JsonResponse(['success' => false, 'message' => 'Name is required'], 400);
        }

        /** @var User $user */
        $user = $this->getUser();

        $database = new IndexingDatabase();
        $database->setName($name);
        $database->setUrl($url ?: null);
        $database->setStatus(IndexingDatabaseStatus::PENDING);
        $database->setCreatedBy($user);

        $this->entityManager->persist($database);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'indexingDatabase.flash.proposed',
            'database' => [
                'id' => $database->getId(),
                'name' => $database->getName(),
            ],
        ]);
    }
}
```

**Note** : Il faut ajouter la méthode `findPendingByCreator` dans le repository :

```php
// Dans IndexingDatabaseRepository.php
public function findPendingByCreator(User $user): array
{
    return $this->createQueryBuilder('idb')
        ->where('idb.status = :status')
        ->andWhere('idb.createdBy = :user')
        ->setParameter('status', IndexingDatabaseStatus::PENDING)
        ->setParameter('user', $user)
        ->orderBy('idb.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}
```

**Résumé du pattern utilisé** :

| Action | `$review` type | Besoin entité Review ? |
|--------|----------------|------------------------|
| `index()` | array | Non - utilise `findByReview($rvid)` |
| `toggle()` | array + entité | Oui - pour manipuler la relation ManyToMany |
| `propose()` | array | Non - ne touche pas Review |

---

## Templates

### Template Admin : indexingDatabase/admin/index.html.twig

Déjà créé - voir fichier `templates/indexingDatabase/admin/index.html.twig`

### Template Journal : indexingDatabase/journal/index.html.twig (avec Stimulus)

**Emplacement** : `templates/indexingDatabase/journal/index.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block body %}
<div class="container"
     data-controller="journal-indexing"
     data-journal-indexing-associated-message-value="{{ 'indexingDatabase.flash.associated'|trans }}"
     data-journal-indexing-dissociated-message-value="{{ 'indexingDatabase.flash.dissociated'|trans }}"
     data-journal-indexing-proposed-message-value="{{ 'indexingDatabase.flash.proposed'|trans }}">

    {% if app.user and app.user.userIdentifier != '__NO_USER__' %}
        {% include 'partials/navbar.html.twig' %}
    {% endif %}

    <div class="row">
        <div class="col-12 pt-4">
            <a href="{{ path('app_journal_dashboard', {'code': code}) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>{{ 'journalDashboard.backToJournalList'|trans }}
            </a>
        </div>
    </div>

    <h1 class="my-4">{{ 'indexingDatabase.journal.title'|trans }}</h1>

    {% include 'components/flash_messages.html.twig' %}

    {# Alert container pour les messages AJAX (Stimulus target) #}
    <div data-journal-indexing-target="alert"></div>

    {# Section : Bases d'indexation disponibles #}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ 'indexingDatabase.journal.availableDatabases'|trans }}</h5>
        </div>
        <div class="card-body">
            {% if databases|length > 0 %}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>{{ 'indexingDatabase.admin.tableName'|trans }}</th>
                            <th>{{ 'indexingDatabase.admin.tableUrl'|trans }}</th>
                            <th class="text-center">{{ 'indexingDatabase.journal.indexed'|trans }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    {% for db in databases %}
                        <tr>
                            <td>
                                {% if db.logo %}
                                    <img src="{{ asset(db.logo) }}" alt="{{ db.name }}" style="height: 24px; margin-right: 8px;">
                                {% endif %}
                                {{ db.name }}
                            </td>
                            <td>
                                {% if db.url %}
                                    <a href="{{ db.url }}" target="_blank" rel="noopener">{{ db.url }}</a>
                                {% else %}
                                    <span class="text-muted">-</span>
                                {% endif %}
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           data-journal-indexing-target="toggle"
                                           data-action="change->journal-indexing#toggle"
                                           data-id="{{ db.id }}"
                                           data-name="{{ db.name }}"
                                           data-url="{{ path('app_journal_indexing_toggle', {'code': code, 'id': db.id}) }}"
                                           data-token="{{ csrf_token('indexing-toggle-' ~ db.id) }}"
                                           {{ db.id in associatedIds ? 'checked' : '' }}>
                                </div>
                            </td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
            {% else %}
                <p class="text-muted mb-0">{{ 'indexingDatabase.admin.noDatabase'|trans }}</p>
            {% endif %}
        </div>
    </div>

    {# Section : Proposer une nouvelle base (si autorisé) #}
    {% if canPropose %}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ 'indexingDatabase.journal.proposeTitle'|trans }}</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">{{ 'indexingDatabase.journal.proposeInfo'|trans }}</p>
            <form data-journal-indexing-target="proposeForm"
                  data-action="submit->journal-indexing#propose"
                  data-url="{{ path('app_journal_indexing_propose', {'code': code}) }}">
                <input type="hidden" name="_token" value="{{ csrf_token('indexing-propose') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="propose-name" class="form-label">{{ 'indexingDatabase.admin.tableName'|trans }} *</label>
                        <input type="text" class="form-control" id="propose-name" name="name" required
                               data-journal-indexing-target="proposeName">
                    </div>
                    <div class="col-md-5">
                        <label for="propose-url" class="form-label">{{ 'indexingDatabase.admin.tableUrl'|trans }}</label>
                        <input type="url" class="form-control" id="propose-url" name="url" placeholder="https://"
                               data-journal-indexing-target="proposeUrl">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i> {{ 'indexingDatabase.journal.propose'|trans }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {% endif %}

    {# Section : Propositions en attente #}
    {% if pendingProposals|length > 0 %}
    <div class="card mb-4">
        <div class="card-header bg-warning-subtle">
            <h5 class="mb-0">{{ 'indexingDatabase.journal.yourPendingProposals'|trans }}</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
            {% for proposal in pendingProposals %}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ proposal.name }}</strong>
                        {% if proposal.url %}
                            <br><small class="text-muted">{{ proposal.url }}</small>
                        {% endif %}
                    </div>
                    <span class="badge bg-warning">{{ 'indexingDatabase.status.pending'|trans }}</span>
                </li>
            {% endfor %}
            </ul>
        </div>
    </div>
    {% endif %}

</div>
{% endblock %}
```

### Stimulus Controller : indexing_database_toggle_controller.js

**Emplacement** : `assets/controllers/journal_indexing_controller.js`

```javascript
import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for journal indexing database management.
 * Handles toggle switches and propose form with AJAX.
 */
export default class extends Controller {
    static targets = ['alert', 'toggle', 'proposeForm', 'proposeName', 'proposeUrl'];

    static values = {
        associatedMessage: String,
        dissociatedMessage: String,
        proposedMessage: String,
    };

    /**
     * Toggle association between journal and indexing database
     */
    async toggle(event) {
        const checkbox = event.target;
        const url = checkbox.dataset.url;
        const token = checkbox.dataset.token;
        const name = checkbox.dataset.name;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: '_token=' + encodeURIComponent(token),
            });

            const data = await response.json();

            if (data.success) {
                const message = data.associated
                    ? this.associatedMessageValue.replace('%name%', name)
                    : this.dissociatedMessageValue.replace('%name%', name);
                this.showAlert('success', message);
            } else {
                checkbox.checked = !checkbox.checked; // Revert
                this.showAlert('danger', data.message);
            }
        } catch (error) {
            checkbox.checked = !checkbox.checked; // Revert
            this.showAlert('danger', 'Error: ' + error.message);
        }
    }

    /**
     * Submit proposal for a new indexing database
     */
    async propose(event) {
        event.preventDefault();

        const form = event.target;
        const url = form.dataset.url;
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams(formData),
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', this.proposedMessageValue);
                form.reset();
                // Reload to show the new pending proposal
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert('danger', data.message);
            }
        } catch (error) {
            this.showAlert('danger', 'Error: ' + error.message);
        }
    }

    /**
     * Display an alert message
     */
    showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        this.alertTarget.insertAdjacentHTML('beforeend', alertHtml);

        // Auto-dismiss after 5 seconds
        const alertElement = this.alertTarget.lastElementChild;
        setTimeout(() => alertElement?.remove(), 5000);
    }
}
```

### Résumé des attributs Stimulus

| Attribut | Rôle |
|----------|------|
| `data-controller="journal-indexing"` | Active le contrôleur Stimulus |
| `data-journal-indexing-target="alert"` | Container pour les messages |
| `data-journal-indexing-target="toggle"` | Checkbox toggle |
| `data-action="change->journal-indexing#toggle"` | Appelle `toggle()` au changement |
| `data-action="submit->journal-indexing#propose"` | Appelle `propose()` à la soumission |
| `data-journal-indexing-associated-message-value` | Message "associé" traduit |
| `data-journal-indexing-dissociated-message-value` | Message "dissocié" traduit |

### Traductions supplémentaires pour le template journal

**messages.fr.yaml** :
```yaml
indexingDatabase:
  journal:
    title: "Référencement & Indexation"
    availableDatabases: "Bases d'indexation disponibles"
    indexed: "Référencé"
    propose: "Proposer"
    proposeTitle: "Proposer une nouvelle base d'indexation"
    proposeInfo: "Votre proposition sera examinée par l'équipe d'administration."
    yourPendingProposals: "Vos propositions en attente"
  flash:
    associated: "La revue est maintenant référencée dans %name%"
    dissociated: "La revue n'est plus référencée dans %name%"
    proposed: "Votre proposition a été soumise"
```

**messages.en.yaml** :
```yaml
indexingDatabase:
  journal:
    title: "Indexing & Referencing"
    availableDatabases: "Available indexing databases"
    indexed: "Indexed"
    propose: "Propose"
    proposeTitle: "Propose a new indexing database"
    proposeInfo: "Your proposal will be reviewed by the administration team."
    yourPendingProposals: "Your pending proposals"
  flash:
    associated: "The journal is now indexed in %name%"
    dissociated: "The journal is no longer indexed in %name%"
    proposed: "Your proposal has been submitted"
```

---

## Ajout du logo dans le formulaire de proposition (Journal)

Cette section décrit comment ajouter un champ d'upload de logo dans le formulaire "Proposer une nouvelle base" côté journal.

### 1. Modifier le template `index.html.twig`

**Fichier** : `templates/indexingDatabase/journal/index.html.twig`

#### a) Ajouter `enctype` au formulaire

```twig
<form data-journal-indexing-target="proposeForm"
      data-action="submit->journal-indexing#propose"
      data-url="{{ path('app_journal_indexing_propose', {'code': code}) }}"
      enctype="multipart/form-data">
```

#### b) Ajuster les colonnes et ajouter le champ logo

Remplacer la section du formulaire par :

```twig
<div class="row g-3">
    <div class="col-md-3">
        <label for="propose-name" class="form-label">{{ 'indexingDatabase.admin.tableName'|trans }} *</label>
        <input type="text" class="form-control" id="propose-name" name="name" required
               data-journal-indexing-target="proposeName">
    </div>
    <div class="col-md-3">
        <label for="propose-url" class="form-label">{{ 'indexingDatabase.admin.tableUrl'|trans }}</label>
        <input type="url" class="form-control" id="propose-url" name="url" placeholder="https://"
               data-journal-indexing-target="proposeUrl">
    </div>
    <div class="col-md-4">
        <label for="propose-logo" class="form-label">{{ 'indexingDatabase.admin.logo'|trans }}</label>
        <input type="file" class="form-control" id="propose-logo" name="logo"
               accept="image/png,image/jpeg,image/gif,image/svg+xml,image/webp"
               data-journal-indexing-target="proposeLogo">
        <small class="text-muted">{{ 'indexingDatabase.journal.logoHint'|trans }}</small>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-paper-plane me-1"></i> {{ 'indexingDatabase.journal.propose'|trans }}
        </button>
    </div>
</div>
```

### 2. Modifier le contrôleur `JournalIndexingController.php`

**Fichier** : `src/Controller/JournalIndexingController.php`

#### a) Ajouter les imports et constantes

```php
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class JournalIndexingController extends AbstractController
{
    private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
    private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
    private const UPLOAD_DIR = 'uploads/indexing-databases';
```

#### b) Ajouter `SluggerInterface` au constructeur

```php
public function __construct(
    private readonly EntityManagerInterface     $entityManager,
    private readonly IndexingDatabaseRepository $repository,
    private readonly ReviewRepository           $reviewRepository,
    private readonly ReviewManager              $reviewManager,
    private readonly SluggerInterface           $slugger,
)
```

#### c) Ajouter la gestion du logo dans `propose()`

Après la validation du nom, ajouter :

```php
// Handle logo upload
$logoPath = null;
$logoFile = $request->files->get('logo');
if ($logoFile instanceof UploadedFile) {
    $extension = strtolower($logoFile->getClientOriginalExtension());

    if (!in_array($extension, self::ALLOWED_LOGO_EXTENSIONS, true)) {
        return new JsonResponse([
            'success' => false,
            'error' => 'Invalid file type. Allowed: ' . implode(', ', self::ALLOWED_LOGO_EXTENSIONS)
        ], Response::HTTP_BAD_REQUEST);
    }

    if ($logoFile->getSize() > self::MAX_LOGO_SIZE) {
        return new JsonResponse([
            'success' => false,
            'error' => 'File too large. Maximum size: 2MB'
        ], Response::HTTP_BAD_REQUEST);
    }

    $safeFilename = $this->slugger->slug(pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME));
    $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/' . self::UPLOAD_DIR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $logoFile->move($uploadDir, $newFilename);
    $logoPath = self::UPLOAD_DIR . '/' . $newFilename;
}
```

Et ajouter après la création de l'entité :

```php
$database->setLogo($logoPath);
```

### 3. Modifier le contrôleur Stimulus

**Fichier** : `assets/controllers/journal_indexing_controller.js`

Modifier la méthode `propose()` pour envoyer un `FormData` (nécessaire pour l'upload de fichiers) :

```javascript
async propose(event) {
    event.preventDefault();

    const form = event.target;
    const url = form.dataset.url;
    const formData = new FormData(form);  // FormData pour supporter les fichiers

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                // NE PAS définir Content-Type, le navigateur le gère automatiquement
            },
            body: formData,  // Envoyer FormData directement (pas URLSearchParams)
        });

        const data = await response.json();

        if (data.success) {
            this.showAlert('success', this.proposedMessageValue);
            form.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            this.showAlert('danger', data.error || data.message);
        }
    } catch (error) {
        this.showAlert('danger', 'Error: ' + error.message);
    }
}
```

**Important** : Ne pas définir le header `Content-Type` quand on envoie un `FormData`. Le navigateur le définit automatiquement avec le bon `boundary` pour `multipart/form-data`.

### 4. Ajouter les traductions

**messages.fr.yaml** :

```yaml
indexingDatabase:
  admin:
    logo: "Logo"
  journal:
    logoHint: "PNG, JPG, GIF, SVG ou WebP (max 2 Mo)"
```

**messages.en.yaml** :

```yaml
indexingDatabase:
  admin:
    logo: "Logo"
  journal:
    logoHint: "PNG, JPG, GIF, SVG or WebP (max 2 MB)"
```

### 5. Résumé des modifications

| Fichier | Modification |
|---------|--------------|
| `index.html.twig` | Ajouter `enctype`, champ file, ajuster colonnes |
| `JournalIndexingController.php` | Imports, constantes, injection `SluggerInterface`, logique upload |
| `journal_indexing_controller.js` | Utiliser `FormData` au lieu de `URLSearchParams` |
| `messages.fr.yaml` / `messages.en.yaml` | Clés `logo` et `logoHint` |

---

## Checklist d'implémentation

- [ ] Créer `IndexingDatabaseStatus` enum
- [ ] Créer `IndexingDatabaseStatusType` Doctrine type
- [ ] Enregistrer le type dans `doctrine.yaml`
- [ ] Créer `IndexingDatabase` entity
- [ ] Modifier `Review` entity (relation ManyToMany inverse)
- [ ] Créer `IndexingDatabaseRepository`
- [ ] Créer et exécuter la migration
- [ ] Créer `IndexingDatabaseVoter`
- [ ] Créer `IndexingDatabaseAdminController`
- [ ] Créer `JournalIndexingController`
- [ ] Créer templates admin (index, create, edit)
- [ ] Créer template journal (index avec toggles)
- [ ] Créer `journal_indexing_controller.js` (Stimulus)
- [ ] Créer `indexing_database_admin_controller.js` (Stimulus)
- [ ] Ajouter traductions FR/EN
- [ ] Build assets (`npm run build`)
- [ ] Tests manuels
- [ ] Tests automatisés

---

## Refactoring proposé : IndexingDatabaseService

### Problème actuel

La logique de gestion des logos et de création des `IndexingDatabase` est **dupliquée** dans deux contrôleurs :

| Élément dupliqué | `IndexingDatabaseAdminController` | `JournalIndexingController` |
|------------------|-----------------------------------|----------------------------|
| `ALLOWED_LOGO_EXTENSIONS` | ligne 20 | ligne 23 |
| `MAX_LOGO_SIZE` | ligne 21 | ligne 24 |
| `UPLOAD_DIR` | ligne 22 | ligne 25 |
| Logique upload logo | `handleLogoUpload()` (lignes 199-243) | inline dans `propose()` (lignes 147-177) |
| Création IndexingDatabase | `handleCreateOrEdit()` | `propose()` |

Cette duplication pose des problèmes de :
- **Maintenabilité** : modifier la validation du logo nécessite des changements à deux endroits
- **Cohérence** : risque de divergence entre les deux implémentations
- **Testabilité** : difficile de tester la logique métier isolément

### Solution proposée

Créer un service `IndexingDatabaseService` qui centralise toute la logique métier.

**Emplacement** : `src/Service/IndexingDatabaseService.php`

### Interface du service

```php
<?php

namespace App\Service;

use App\Entity\IndexingDatabase;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class IndexingDatabaseService
{
    private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
    private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
    private const UPLOAD_DIR = 'data/indexing-databases';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {}

    /**
     * Crée une nouvelle base d'indexation.
     *
     * Garantit la cohérence : si l'insertion en BDD échoue, le fichier uploadé est supprimé.
     *
     * @throws \InvalidArgumentException Si le logo est invalide
     */
    public function create(
        string $name,
        ?string $url,
        ?UploadedFile $logo,
        User $createdBy,
        IndexingDatabaseStatus $status = IndexingDatabaseStatus::PENDING
    ): IndexingDatabase {
        // 1. Upload du logo AVANT la création en BDD
        $logoPath = null;
        if ($logo !== null) {
            $logoPath = $this->uploadLogo($logo, $name);
        }

        // 2. Créer l'entité
        $database = new IndexingDatabase();
        $database->setName($name);
        $database->setUrl($url);
        $database->setLogo($logoPath);
        $database->setStatus($status);
        $database->setCreatedBy($createdBy);

        // 3. Persister avec rollback du fichier si échec
        try {
            $this->entityManager->persist($database);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            // Supprimer le fichier uploadé si l'insertion BDD échoue
            if ($logoPath !== null) {
                $this->deleteLogo($logoPath);
            }
            throw $e;
        }

        return $database;
    }

    /**
     * Met à jour une base d'indexation existante.
     *
     * Garantit la cohérence :
     * - L'ancien logo n'est supprimé qu'après succès du flush
     * - Si le flush échoue, le nouveau fichier est supprimé
     *
     * @throws \InvalidArgumentException Si le logo est invalide
     */
    public function update(
        IndexingDatabase $database,
        string $name,
        ?string $url,
        ?UploadedFile $newLogo = null,
        bool $removeLogo = false
    ): void {
        $oldLogoPath = $database->getLogo();
        $newLogoPath = null;

        $database->setName($name);
        $database->setUrl($url);
        $database->setUpdatedAt(new \DateTime());

        // 1. Upload du nouveau logo AVANT de toucher à l'ancien
        if ($newLogo !== null) {
            $newLogoPath = $this->uploadLogo($newLogo, $name);
            $database->setLogo($newLogoPath);
        } elseif ($removeLogo) {
            $database->setLogo(null);
        }

        // 2. Flush avec gestion d'erreur
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            // Si échec, supprimer le nouveau fichier uploadé
            if ($newLogoPath !== null) {
                $this->deleteLogo($newLogoPath);
            }
            throw $e;
        }

        // 3. Supprimer l'ancien logo APRÈS succès du flush
        if ($oldLogoPath !== null && ($newLogoPath !== null || $removeLogo)) {
            $this->deleteLogo($oldLogoPath);
        }
    }

    /**
     * Supprime une base d'indexation et son logo.
     */
    public function delete(IndexingDatabase $database): void
    {
        if ($database->getLogo()) {
            $this->deleteLogo($database->getLogo());
        }

        $this->entityManager->remove($database);
        $this->entityManager->flush();
    }

    /**
     * Upload un logo et retourne le nom du fichier (pas le chemin complet).
     *
     * @throws \InvalidArgumentException Si le fichier est invalide
     */
    public function uploadLogo(UploadedFile $file, ?string $name = null): string
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Logo upload failed');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_LOGO_EXTENSIONS, true)) {
            throw new \InvalidArgumentException(
                'Invalid logo format. Allowed: ' . implode(', ', self::ALLOWED_LOGO_EXTENSIONS)
            );
        }

        if ($file->getSize() > self::MAX_LOGO_SIZE) {
            throw new \InvalidArgumentException('Logo file too large. Maximum size: 2MB');
        }

        $slug = $this->slugger->slug($name ?: 'database')->lower();
        $newFilename = $slug . '-' . uniqid() . '.' . $extension;

        $uploadDir = $this->projectDir . '/' . self::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file->move($uploadDir, $newFilename);

        // Retourne seulement le nom du fichier (stocké en BDD)
        return $newFilename;
    }

    /**
     * Supprime un fichier logo du système de fichiers.
     *
     * @param string $logoName Le nom du fichier (pas le chemin complet)
     */
    public function deleteLogo(string $logoName): void
    {
        $fullPath = $this->projectDir . '/' . self::UPLOAD_DIR . '/' . $logoName;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Retourne les extensions de logo autorisées.
     */
    public function getAllowedExtensions(): array
    {
        return self::ALLOWED_LOGO_EXTENSIONS;
    }

    /**
     * Retourne la taille maximale du logo en octets.
     */
    public function getMaxLogoSize(): int
    {
        return self::MAX_LOGO_SIZE;
    }
}
```

### Gestion des erreurs

Le service utilise `\InvalidArgumentException` pour les erreurs de validation du logo (format invalide, taille trop grande). Pas besoin d'exception personnalisée pour ce cas simple.

### Garanties de cohérence fichier/BDD

Le service garantit que le chemin stocké en base de données correspond toujours à un fichier réel sur le serveur :

| Opération | Ordre des actions | Rollback si échec |
|-----------|-------------------|-------------------|
| **create()** | 1. Upload fichier → 2. Insert BDD | Supprime le fichier uploadé |
| **update()** | 1. Upload nouveau → 2. Update BDD → 3. Supprime ancien | Supprime le nouveau fichier |
| **delete()** | 1. Delete BDD → 2. Supprime fichier | Aucun (fichier orphelin acceptable) |

**Principe clé** : Ne jamais supprimer l'ancien fichier avant que le flush() réussisse.

```
┌─────────────────────────────────────────────────────────────┐
│                     create() / update()                      │
├─────────────────────────────────────────────────────────────┤
│  1. Valider le fichier                                      │
│  2. Générer nom unique (slug + uniqid)                      │
│  3. Déplacer le fichier vers uploads/                       │
│  4. Mettre à jour l'entité avec le chemin                   │
│  5. flush() ──┬── succès → supprimer ancien fichier (si any)│
│               └── échec  → supprimer nouveau fichier        │
└─────────────────────────────────────────────────────────────┘
```

**Nom de fichier unique** : `{slug}-{uniqid}.{ext}` évite les collisions (ex: `doaj-66a1b2c3d4e5f.png`).

### Contrôleurs simplifiés après refactoring

#### IndexingDatabaseAdminController (après)

```php
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

    return $this->render('indexingDatabase/admin/create.html.twig');
}
```

#### JournalIndexingController (après)

```php
#[Route('/propose', name: 'app_journal_indexing_propose', methods: ['POST'])]
public function propose(string $code, Request $request): JsonResponse
{
    $reviewData = $this->reviewManager->getReviewByCode($code);
    if (!$reviewData) {
        return new JsonResponse(['success' => false, 'error' => 'Review not found'], 404);
    }

    $this->denyAccessUnlessGranted(IndexingDatabaseVoter::PROPOSE, $reviewData);

    if (!$this->isCsrfTokenValid('indexing-propose', $request->request->get('_token'))) {
        return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], 403);
    }

    $name = trim($request->request->get('name', ''));
    if (empty($name)) {
        return new JsonResponse(['success' => false, 'error' => 'Name is required'], 400);
    }

    try {
        $database = $this->indexingDatabaseService->create(
            name: $name,
            url: trim($request->request->get('url', '')) ?: null,
            logo: $request->files->get('logo'),
            createdBy: $this->getUser(),
            status: IndexingDatabaseStatus::PENDING
        );

        return new JsonResponse([
            'success' => true,
            'database' => [
                'id' => $database->getId(),
                'name' => $database->getName(),
            ],
        ]);
    } catch (\InvalidArgumentException $e) {
        return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
    }
}
```

### Avantages du refactoring

| Aspect | Avant | Après |
|--------|-------|-------|
| **Duplication** | Constantes et logique dupliquées | Source unique de vérité |
| **Testabilité** | Tests difficiles (contrôleurs) | Service facilement testable |
| **Maintenabilité** | 2 endroits à modifier | 1 seul endroit |
| **Lisibilité contrôleurs** | ~100 lignes de logique | ~20 lignes, délégation claire |
| **Réutilisabilité** | Aucune | API, CLI, autres contrôleurs |

### Plan d'implémentation

1. [ ] Créer `src/Service/IndexingDatabaseService.php`
2. [ ] Modifier `IndexingDatabaseAdminController` pour utiliser le service
3. [ ] Modifier `JournalIndexingController` pour utiliser le service
4. [ ] Supprimer les constantes et méthodes dupliquées des contrôleurs
5. [ ] Créer `tests/php/Unit/IndexingDatabaseServiceTest.php`
6. [ ] Tester manuellement les deux flux (admin et journal)

> **Note** : Pas besoin de configuration `services.yaml`. L'attribut `#[Autowire('%kernel.project_dir%')]` gère l'injection automatiquement.
