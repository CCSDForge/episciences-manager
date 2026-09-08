# Gestion des Ressources (Resources Management)

## Vue d'ensemble

Le système de gestion des ressources permet aux utilisateurs d'uploader, gérer et supprimer des fichiers (images, documents) qui peuvent être utilisés dans les **Pages** et les **Actualités (News)** d'un journal.

## Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Frontend (JavaScript)                        │
│                    assets/scripts/pages/resources.js                 │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         ResourcesController                          │
│                   src/Controller/ResourcesController.php             │
│  Routes:                                                             │
│  - GET  /journal/{code}/resources           → Liste des fichiers     │
│  - POST /journal/{code}/resources/upload    → Upload fichier         │
│  - DELETE /journal/{code}/resources/{file}/delete → Suppression      │
│  - GET  /journal/{code}/resources/{file}/check-usage → Vérif usage   │
│  - GET  /{code}/resources/{filename}        → Servir le fichier      │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        ResourceUsageService                          │
│                  src/Service/ResourceUsageService.php                │
│  Méthodes:                                                           │
│  - findResourceUsageInPages()  → Recherche dans les pages            │
│  - findResourceUsageInNews()   → Recherche dans les actualités       │
│  - getResourceUsageSummary()   → Résumé complet d'utilisation        │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    ▼                               ▼
        ┌───────────────────┐           ┌───────────────────┐
        │   PageRepository  │           │   NewsRepository  │
        │    Entity: Page   │           │    Entity: News   │
        └───────────────────┘           └───────────────────┘
```

## Stockage des fichiers

Les ressources sont stockées dans le système de fichiers :

```
data/
└── {journal_code}/
    └── public/
        ├── image1.png
        ├── document.pdf
        └── ...
```

**Service**: `UploadDirectoryService` (`src/Service/UploadDirectoryService.php`)

| Méthode | Chemin retourné |
|---------|-----------------|
| `getUploadDirectory($code)` | `data/{code}/public/` |
| `getJournalDataPath($code)` | `data/{code}/` |
| `getJournalTmpPath($code)` | `data/{code}/tmp/` |
| `getJournalFilesPath($code)` | `data/{code}/files/` |

## Structure des données

### Page (Entity)

```php
class Page {
    int $id;
    string $rvcode;           // Code du journal
    string $page_code;        // Identifiant unique de la page
    array $title;             // ["en" => "Title", "fr" => "Titre"]
    array $content;           // ["en" => "<p>Content...</p>", "fr" => "<p>Contenu...</p>"]
    array $visibility;        // ["public"] ou ["private"]
}
```

### News (Entity)

```php
class News {
    int $id;
    string $rvcode;           // Code du journal
    array $title;             // ["en" => "Title", "fr" => "Titre"]
    array $content;           // ["en" => "<p>Content...</p>", "fr" => "<p>Contenu...</p>"]
    array $link;              // ["en" => "https://...", "fr" => "https://..."]
    array $visibility;        // ["public"] ou ["private"]
    User $creator;
    DateTime $dateCreation;
    DateTime $dateUpdated;
}
```

## Flux de suppression d'une ressource

### 1. Chargement de la page (PHP pré-calcule les données)

```php
// ResourcesController.php - index()
public function index(string $code, UploadDirectoryService $dirs, ResourceUsageService $usageService): Response
{
    $files = $this->getFilesForJournal($code, $dirs);

    // Pré-calculer l'utilisation pour chaque fichier
    foreach ($files as &$file) {
        $file['usage'] = $usageService->getResourceUsageSummary($file['name'], $code);
    }

    return $this->render('resources/index.html.twig', ['files' => $files]);
}
```

### 2. Données intégrées dans le HTML (Twig)

```twig
<button class="delete-file-btn"
        data-filename="{{ file.name }}"
        data-usage="{{ file.usage|json_encode|e('html_attr') }}">
    <i class="fas fa-trash"></i>
</button>
```

### 3. Clic sur le bouton supprimer (JavaScript lit les données)

```javascript
// resources.js - Pas d'appel API, lecture directe du HTML
document.addEventListener('click', function (e) {
    const deleteBtn = e.target.closest('.delete-file-btn');
    if (deleteBtn) {
        const filename = deleteBtn.getAttribute('data-filename');

        // Données déjà dans le HTML (pré-calculées par PHP)
        const usageData = JSON.parse(deleteBtn.getAttribute('data-usage') || '{}');

        deleteConfirmModal.show();
        displayResourceUsage(usageData);  // Affichage instantané
    }
});
```

### 4. Détection des références (Service)

**ResourceUsageService** recherche le fichier dans le contenu avec ces patterns :

| Pattern | Description |
|---------|-------------|
| `/{rvcode}/resources/{filename}` | URL directe |
| `resources/{filename}` | URL relative |
| `src="...{filename}..."` | Attribut HTML img src |
| `href="...{filename}..."` | Attribut HTML href |
| `![...](...)` | Image Markdown |
| `[...](...)` | Lien Markdown |
| `{filename}` | Référence directe |

### 5. Structure des données d'utilisation

```json
{
    "inUse": true,
    "pageCount": 2,
    "newsCount": 1,
    "pages": [
        {
            "page_code": "home",
            "title": {"en": "Home", "fr": "Accueil"},
            "locationCount": 3,
            "type": "page"
        }
    ],
    "news": [
        {
            "news_id": 42,
            "title": {"en": "New publication", "fr": "Nouvelle publication"},
            "locationCount": 1,
            "type": "news"
        }
    ]
}
```

### 6. Affichage dans la modal

**Si le fichier est utilisé** (alert warning jaune) :

```
⚠️ Fichier en cours d'utilisation

Ce fichier est actuellement utilisé. Le supprimer pourrait
casser l'affichage de pages ou d'actualités.

📄 Pages utilisant ce fichier :
  • Accueil (home) - 3 references
  • À propos (about)

📰 Actualités utilisant ce fichier :
  • Nouvelle publication - 1 reference

[Annuler]  [Supprimer quand même]  ← bouton orange
```

**Si le fichier n'est pas utilisé** (alert success vert) :

```
✅ Ce fichier n'est utilisé dans aucune page ou actualité

Il peut être supprimé en toute sécurité.

[Annuler]  [Supprimer]  ← bouton rouge
```

### 7. Suppression effective

**Route**: `DELETE /journal/{code}/resources/{filename}/delete`

Avec validation CSRF et vérification de sécurité (path traversal).

## Architecture : Sans API (pré-chargement PHP)

```
┌─────────────────────────────────────────────────────────────────────┐
│                      1. Requête GET /resources                       │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      PHP (ResourcesController)                       │
│  - Liste les fichiers                                                │
│  - Calcule l'utilisation pour CHAQUE fichier                        │
│  - Génère le HTML avec data-usage="..."                             │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                           HTML rendu                                 │
│  <button data-filename="logo.png"                                   │
│          data-usage='{"inUse":true,"pages":[...],"news":[...]}'>    │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    JavaScript (clic supprimer)                       │
│  const usage = JSON.parse(btn.dataset.usage);  // Instantané !      │
│  displayResourceUsage(usage);                  // Pas d'Ajax        │
└─────────────────────────────────────────────────────────────────────┘
```

**Avantages** :
- Pas d'appel réseau au clic → affichage instantané
- Code JavaScript plus simple
- Moins de latence pour l'utilisateur

**Inconvénients** :
- Chargement initial plus long si beaucoup de fichiers
- Données potentiellement obsolètes si modifiées par un autre utilisateur

## Traductions

| Clé | FR | EN |
|-----|----|----|
| `resources.confirmDelete` | Confirmer la suppression | Confirm Deletion |
| `resources.deleteConfirmMessage` | Êtes-vous sûr de vouloir supprimer cette ressource ? Cette action est irréversible. | Are you sure you want to delete this resource? This action cannot be undone. |
| `resources.fileInUseWarning` | Fichier en cours d'utilisation | File currently in use |
| `resources.fileInUseMessage` | Ce fichier est actuellement utilisé. Le supprimer pourrait casser l'affichage de pages ou d'actualités. | This file is currently being used. Deleting it may break the display of pages or news. |
| `resources.deleteAnyway` | Supprimer quand même | Delete anyway |
| `resources.fileNotInUse` | Ce fichier n'est utilisé dans aucune page ou actualité | This file is not used in any page or news. |
| `resources.safeToDelete` | Il peut être supprimé en toute sécurité | It can be safely deleted. |
| `resources.pagesUsingFile` | Pages utilisant ce fichier : | Pages using this file: |
| `resources.newsUsingFile` | Actualités utilisant ce fichier : | News using this file: |
| `resources.deleteSuccess` | Ressource supprimée avec succès | Resource deleted successfully |
| `resources.deleteError` | Erreur lors de la suppression | Error during deletion |

## Fichiers impliqués

| Fichier | Rôle |
|---------|------|
| `src/Controller/ResourcesController.php` | API REST pour les ressources |
| `src/Service/ResourceUsageService.php` | Détection d'utilisation des fichiers |
| `src/Service/UploadDirectoryService.php` | Gestion des chemins de stockage |
| `src/Entity/Page.php` | Entité Page |
| `src/Entity/News.php` | Entité News |
| `src/Repository/PageRepository.php` | Accès aux pages |
| `src/Repository/NewsRepository.php` | Accès aux actualités |
| `templates/resources/index.html.twig` | Interface utilisateur + modal |
| `assets/scripts/pages/resources.js` | Logique JavaScript frontend |
| `translations/messages.fr.yaml` | Traductions FR |
| `translations/messages.en.yaml` | Traductions EN |

## Optimisation de performance

### Requêtes SQL optimisées

Au lieu de charger toutes les pages/news puis filtrer en PHP, on utilise `LIKE` directement en SQL.

#### Avant : `findBy(['rvcode' => $rvcode])`

```php
// Charge TOUTES les pages du journal
$pages = $this->pageRepository->findBy(['rvcode' => $rvcode]);
```

```sql
SELECT * FROM pages WHERE rvcode = 'epijinfo'
-- Résultat: 100 pages chargées en mémoire
```

Ensuite, filtre en PHP avec regex pour trouver celles qui contiennent le fichier :

```php
foreach ($pages as $page) {  // 100 itérations
    if (preg_match('/logo\.png/', $page->getContent())) {
        // Trouvé !
    }
}
```

**Problème** : Charge beaucoup de données inutiles en mémoire.

#### Après : `findByContentContaining($filename, $rvcode)`

```php
// PageRepository / NewsRepository
public function findByContentContaining(string $filename, string $rvcode): array
{
    return $this->createQueryBuilder('p')
        ->where('p.rvcode = :rvcode')
        ->andWhere('p.content LIKE :filename')
        ->setParameter('rvcode', $rvcode)
        ->setParameter('filename', '%' . $filename . '%')
        ->getQuery()
        ->getResult();
}
```

```sql
SELECT * FROM pages WHERE rvcode = 'epijinfo' AND content LIKE '%logo.png%'
-- Résultat: 2 pages chargées en mémoire
```

Le filtrage est fait par MySQL, puis vérification précise en PHP seulement sur les résultats :

```php
$pages = $this->pageRepository->findByContentContaining($filename, $rvcode);
foreach ($pages as $page) {  // 2 itérations seulement
    if (preg_match('/logo\.png/', $page->getContent())) {
        // Trouvé !
    }
}
```

**Avantage** : Moins de données en mémoire, plus rapide.

#### Illustration du flux

```
Journal avec 100 pages, 2 utilisent "logo.png"

AVANT (findBy):
  ┌─────────┐      ┌─────────────────┐      ┌─────────────┐
  │   BDD   │ ──── │  100 pages      │ ──── │  PHP filtre │ ──── 2 pages trouvées
  └─────────┘      │  en mémoire     │      │  avec regex │
                   └─────────────────┘      └─────────────┘
                          ▲
                          └── Lent, beaucoup de mémoire

APRÈS (findByContentContaining):
  ┌─────────────────┐      ┌─────────────┐      ┌─────────────┐
  │  BDD filtre     │ ──── │  2 pages    │ ──── │  PHP vérifie│ ──── 2 pages trouvées
  │  avec LIKE      │      │  en mémoire │      │  avec regex │
  └─────────────────┘      └─────────────┘      └─────────────┘
         ▲
         └── Rapide, peu de mémoire
```

### Performance comparée

| Aspect | Avant | Après |
|--------|-------|-------|
| Requête SQL | `SELECT * WHERE rvcode = ?` | `SELECT * WHERE rvcode = ? AND content LIKE ?` |
| Entités chargées | Toutes (100 pages, 200 news) | Seulement celles qui matchent (2-5) |
| Filtrage principal | En PHP (lent) | En SQL (rapide) |
| Mémoire PHP | Élevée | Faible |
| Temps estimé | ~2s | ~0.2s |

### Pourquoi garder le filtrage PHP après SQL ?

Le `LIKE '%filename%'` en SQL peut avoir des **faux positifs** :
- `logo.png` peut matcher `my-logo.png` ou `logo.png.bak`

Le regex en PHP permet une vérification plus précise du contexte (URL, attribut src, markdown, etc.).

## Sécurité

1. **CSRF Token** : Validé pour toutes les opérations sensibles (upload, delete)
2. **Path Traversal** : Protection contre les attaques `../` via `realpath()`
3. **Extensions autorisées** : Liste blanche d'extensions
4. **Taille max** : 10 MB par fichier