# Refactoring : Suppression des doublons dans la fonctionnalité Indexing Databases

## Contexte

La fonctionnalité de gestion des bases d'indexation contenait plusieurs duplications de code :
- Constantes définies 3 fois dans différents fichiers
- Logique d'upload de logo dupliquée dans 3 endroits
- Structure de table HTML répétée 3 fois dans le template Twig

## Modifications effectuées

### 1. Centralisation des constantes

**Avant** : Les constantes suivantes étaient définies dans 3 fichiers différents :

| Constante | Fichiers |
|-----------|----------|
| `ALLOWED_LOGO_EXTENSIONS` | Service, AdminController, JournalController |
| `MAX_LOGO_SIZE` | idem |
| `UPLOAD_DIR` | idem + LogoController |

**Après** : Toutes les constantes sont uniquement dans `IndexingDatabaseService` :

```php
// src/Service/IndexingDatabaseService.php
private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
private const UPLOAD_DIR = 'data/indexing-databases';
```

Les constantes sont accessibles via des méthodes publiques :
- `getAllowedExtensions(): array`
- `getMaxLogoSize(): int`
- `getUploadDirectory(): string`

### 2. Centralisation de la logique d'upload

**Avant** : La logique d'upload était dupliquée dans :
- `IndexingDatabaseService::uploadLogo()` (lignes 108-136)
- `IndexingDatabaseAdminController::handleLogoUpload()` (lignes 229-271)
- `JournalIndexingController::propose()` inline (lignes 168-202)

**Après** : Seul `IndexingDatabaseService` gère l'upload via ses méthodes :

```php
// Création d'une nouvelle base d'indexation
$database = $this->indexingDatabaseService->create(
    name: $name,
    url: $url,
    logo: $uploadedFile,  // UploadedFile|null
    createdBy: $user,
    status: IndexingDatabaseStatus::PENDING
);

// Mise à jour d'une base existante
$this->indexingDatabaseService->update(
    database: $database,
    name: $name,
    url: $url,
    newLogo: $uploadedFile,    // UploadedFile|null
    removeLogo: $removeLogo    // bool
);

// Suppression (gère aussi la suppression du fichier logo)
$this->indexingDatabaseService->delete($database);
```

### 3. Modifications des contrôleurs

#### IndexingDatabaseAdminController

- Suppression des constantes privées
- Suppression de la méthode `handleLogoUpload()` (43 lignes)
- Suppression de l'injection de `SluggerInterface` (non utilisé)
- `handleCreateOrEdit()` : utilise `$this->indexingDatabaseService->create()` et `->update()`
- `delete()` : utilise `$this->indexingDatabaseService->delete()`

#### JournalIndexingController

- Suppression des constantes privées
- Ajout de l'injection de `IndexingDatabaseService`
- Suppression de l'injection de `SluggerInterface` (non utilisé)
- `propose()` : suppression de 35 lignes de logique inline, utilise `$this->indexingDatabaseService->create()`

#### IndexingDatabaseLogoController

- Suppression de la constante `UPLOAD_DIR`
- Ajout de l'injection de `IndexingDatabaseService`
- Utilise `$this->indexingDatabaseService->getUploadDirectory()` pour obtenir le chemin

### 4. Factorisation du template Twig

**Avant** : Le même pattern de table était répété 3 fois dans `journal/index.html.twig` :
- Lignes 44-89 : databases disponibles (avec toggle switch)
- Lignes 108-143 : pending proposals (avec badge statut)
- Lignes 155-188 : rejected proposals (avec badge statut)

**Après** : Création d'un partial `_database_row.html.twig` :

```twig
{# templates/indexingDatabase/journal/_database_row.html.twig #}

{# Variables:
   - database: IndexingDatabase entity
   - lastColumn: 'toggle' | 'status'
   - code: journal code (si lastColumn = 'toggle')
   - associatedIds: array (si lastColumn = 'toggle')
#}
```

Utilisation dans le template principal :

```twig
{# Table des databases disponibles #}
{% for db in indexingDatabases %}
    {% include 'indexingDatabase/journal/_database_row.html.twig' with {
        database: db,
        lastColumn: 'toggle',
        code: code,
        associatedIds: associatedIds
    } %}
{% endfor %}

{# Table des propositions en attente #}
{% for proposal in pendingProposals %}
    {% include 'indexingDatabase/journal/_database_row.html.twig' with {
        database: proposal,
        lastColumn: 'status'
    } %}
{% endfor %}
```

## Structure finale

```
src/
├── Service/
│   └── IndexingDatabaseService.php      # Source unique pour upload/constantes
├── Controller/
│   ├── JournalIndexingController.php    # Utilise le service
│   ├── IndexingDatabaseLogoController.php # Utilise le service
│   └── Admin/
│       └── IndexingDatabaseAdminController.php # Utilise le service

templates/indexingDatabase/
├── journal/
│   ├── index.html.twig                  # Template principal
│   └── _database_row.html.twig          # Partial pour les lignes de table
```

## API du service

```php
class IndexingDatabaseService
{
    // Création
    public function create(
        string $name,
        ?string $url,
        ?UploadedFile $logo,
        User $createdBy,
        IndexingDatabaseStatus $status = IndexingDatabaseStatus::PENDING
    ): IndexingDatabase;

    // Mise à jour
    public function update(
        IndexingDatabase $database,
        string $name,
        ?string $url,
        ?UploadedFile $newLogo = null,
        bool $removeLogo = false
    ): void;

    // Suppression
    public function delete(IndexingDatabase $database): void;

    // Upload de logo (utilisé en interne)
    public function uploadLogo(UploadedFile $file, ?string $name = null): string;

    // Suppression de logo
    public function deleteLogo(string $logoFilename): void;

    // Accesseurs
    public function getAllowedExtensions(): array;
    public function getMaxLogoSize(): int;
    public function getUploadDirectory(): string;
}
```

## Gestion des erreurs

Le service lève des `\InvalidArgumentException` pour les erreurs de validation :
- "Logo upload failed" - fichier invalide
- "Invalid logo format. Allowed: ..." - extension non autorisée
- "Logo file too large. Maximum size: 2MB" - fichier trop volumineux

Les contrôleurs capturent ces exceptions et affichent les messages appropriés via flash messages ou JSON responses.