# Workflow Markdown et CKEditor

## Vue d'ensemble

Le système utilise **Markdown** comme format de stockage pour le contenu des pages et des actualités. L'éditeur **CKEditor 5** avec le plugin **GitHub Flavored Markdown (GFM)** permet une édition visuelle tout en produisant du Markdown.

## Flux de données

```
┌─────────────────────────────────────────────────────────────────┐
│                        ÉDITION                                  │
│                                                                 │
│   Base de données (Markdown)                                    │
│           │                                                     │
│           ▼                                                     │
│   CKEditor (entrée Markdown → édition visuelle → sortie Markdown)│
│           │                                                     │
│           ▼                                                     │
│   Base de données (Markdown)                                    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                       AFFICHAGE                                 │
│                                                                 │
│   Base de données (Markdown)                                    │
│           │                                                     │
│           ▼                                                     │
│   MarkdownService::toHtml()                                     │
│           │                                                     │
│           ▼                                                     │
│   Template Twig (HTML)                                          │
└─────────────────────────────────────────────────────────────────┘
```

## Qu'est-ce que Markdown ?

**Markdown** est un langage de balisage léger créé en 2004. Il permet d'écrire du contenu formaté en utilisant une syntaxe simple et lisible.

### Syntaxe de base

| Élément | Syntaxe Markdown | Résultat HTML |
|---------|------------------|---------------|
| Gras | `**texte**` | `<strong>texte</strong>` |
| Italique | `_texte_` ou `*texte*` | `<em>texte</em>` |
| Titre H1 | `# Titre` | `<h1>Titre</h1>` |
| Titre H2 | `## Titre` | `<h2>Titre</h2>` |
| Titre H3 | `### Titre` | `<h3>Titre</h3>` |
| Lien | `[texte](url)` | `<a href="url">texte</a>` |
| Image | `![alt](url)` | `<img src="url" alt="alt">` |
| Liste à puces | `- item` | `<ul><li>item</li></ul>` |
| Liste numérotée | `1. item` | `<ol><li>item</li></ol>` |
| Citation | `> texte` | `<blockquote>texte</blockquote>` |
| Code inline | `` `code` `` | `<code>code</code>` |
| Bloc de code | ` ```lang ` | `<pre><code>...</code></pre>` |

## GitHub Flavored Markdown (GFM)

**GFM** est une extension de Markdown créée par GitHub. Elle ajoute des fonctionnalités supplémentaires :

### Extensions GFM

| Fonctionnalité | Syntaxe | Description |
|----------------|---------|-------------|
| Tableaux | `\| col1 \| col2 \|` | Création de tableaux |
| Barré | `~~texte~~` | Texte barré |
| Listes de tâches | `- [ ] tâche` | Cases à cocher |
| Autolinks | `https://example.com` | Liens automatiques |
| Retours à la ligne | Deux espaces en fin de ligne | Saut de ligne sans paragraphe |

### Exemple de tableau GFM

```markdown
| Colonne 1 | Colonne 2 | Colonne 3 |
|-----------|-----------|-----------|
| Cellule 1 | Cellule 2 | Cellule 3 |
| Cellule 4 | Cellule 5 | Cellule 6 |
```

## CKEditor 5 avec plugin Markdown

### Configuration

Le fichier `assets/scripts/components/ckeditor.js` configure CKEditor avec le plugin GFM :

```javascript
import { Markdown } from '@ckeditor/ckeditor5-markdown-gfm';

// Dans la configuration des plugins
plugins: [
  // ... autres plugins
  Markdown,  // Active le mode Markdown
]
```

### Comportement du plugin Markdown

Quand le plugin `Markdown` est activé :

1. **Entrée** : CKEditor attend du contenu Markdown
2. **Édition** : L'utilisateur édite visuellement (WYSIWYG)
3. **Sortie** : `editor.getData()` retourne du Markdown (pas du HTML)

### Fonctionnalités disponibles

L'éditeur inclut :
- Formatage de base (gras, italique)
- Titres (H1-H6)
- Listes (à puces, numérotées)
- Liens et images
- Tableaux
- Blocs de citation
- Blocs de code
- Alignement
- Caractères spéciaux

## Stockage en base de données

### Structure JSON multilingue

Le contenu est stocké en JSON avec une clé par langue :

```json
{
  "en": "## English Title\n\nEnglish content with **bold** text.",
  "fr": "## Titre Français\n\nContenu français avec du texte **gras**.",
  "es": ""
}
```

### Entités concernées

| Entité | Table | Colonne |
|--------|-------|---------|
| Page | `pages` | `content` (JSON) |
| News | `news` | `content` (JSON) |

## Conversion Markdown → HTML

### MarkdownService

Le service `src/Service/MarkdownService.php` convertit le Markdown en HTML pour l'affichage :

```php
use League\CommonMark\GithubFlavoredMarkdownConverter;

class MarkdownService
{
    private GithubFlavoredMarkdownConverter $converter;

    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',       // Supprime le HTML brut
            'allow_unsafe_links' => false, // Bloque javascript:, etc.
        ]);
    }

    public function toHtml(?string $markdown): string
    {
        if ($markdown === null || trim($markdown) === '') {
            return '';
        }
        return $this->converter->convert($markdown)->getContent();
    }

    public function convertContentArray(array $content): array
    {
        $out = [];
        foreach ($content as $locale => $markdown) {
            $out[$locale] = $this->toHtml($markdown);
        }
        return $out;
    }
}
```

### Utilisation dans les contrôleurs

```php
// PageController.php - Affichage
$htmlContent = $markdownService->convertContentArray($page->getContent());

$currentPageData = [
    'content' => $htmlContent,           // HTML pour affichage
    'markdownContent' => $page->getContent(), // Markdown pour édition
];
```

## Sécurité

### Protection contre le HTML malveillant

1. **À l'entrée** : CKEditor avec plugin Markdown ignore le HTML brut
2. **Au stockage** : Le contenu est du Markdown pur
3. **À l'affichage** : `html_input => 'strip'` supprime tout HTML dans le Markdown
4. **Liens** : `allow_unsafe_links => false` bloque `javascript:`, `data:`, etc.

### Configuration de sécurité CKEditor

```javascript
// ckeditor.js
image: {
  insert: {
    integrations: ['url'],  // Uniquement insertion par URL
  },
},
// allow_unsafe_links est désactivé par défaut
```

## Librairies utilisées

| Librairie | Version | Usage |
|-----------|---------|-------|
| `ckeditor5` | ^45.2.0 | Éditeur WYSIWYG |
| `@ckeditor/ckeditor5-markdown-gfm` | ^45.2.0 | Plugin GFM pour CKEditor |
| `league/commonmark` | ^2.8 | Conversion Markdown → HTML (PHP) |

## Fichiers clés

| Fichier | Rôle |
|---------|------|
| `assets/scripts/components/ckeditor.js` | Configuration CKEditor |
| `assets/scripts/pages/journalPages.js` | Gestion édition pages |
| `assets/scripts/pages/news.js` | Gestion édition actualités |
| `src/Service/MarkdownService.php` | Conversion Markdown → HTML |
| `src/Controller/PageController.php` | Sauvegarde/affichage pages |
| `src/Controller/NewsController.php` | Sauvegarde/affichage actualités |

## Résumé

1. **Stockage** : Markdown en JSON multilingue
2. **Édition** : CKEditor 5 + plugin GFM (entrée/sortie Markdown)
3. **Affichage** : Conversion via `league/commonmark` (Markdown → HTML)
4. **Sécurité** : Pas de HTML brut, liens sécurisés