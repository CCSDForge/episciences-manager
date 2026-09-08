# Stockage et affichage du contenu Markdown

## Vue d'ensemble

L'application utilise **Markdown** pour stocker le contenu des pages et des news. Le contenu est converti en **HTML** uniquement pour l'affichage.

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   CKEditor   │ ───► │    Base de   │ ───► │  Affichage   │
│  (Markdown)  │      │   données    │      │    (HTML)    │
│              │      │  (Markdown)  │      │              │
└──────────────┘      └──────────────┘      └──────────────┘
```

## Pourquoi Markdown ?

1. **Attributs d'image** : Markdown étendu supporte `{width="50%" style="float:left"}`
2. **Portabilité** : Format texte simple, facile à migrer
3. **Sécurité** : Pas de HTML brut stocké (évite XSS)
4. **Versioning** : Diff lisible dans Git

## Les deux méthodes de chargement

### Méthode 1 : Objet JavaScript global (Pages)

Utilisée pour les **pages** car on édite une seule page à la fois.

#### Implémentation

**Controller (PageController.php) :**
```php
$currentPageData = [
    'title' => $title,
    'content' => $htmlContent,           // HTML pour affichage
    'markdownContent' => $page->getContent(), // Markdown brut
];
```

**Template (journalPages.html.twig) :**
```javascript
window.pageConfig = {
    pageData: {
        title: {{ currentPageData.title|json_encode|raw }},
        content: {{ currentPageData.content|json_encode|raw }},
        markdownContent: {{ currentPageData.markdownContent|json_encode|raw }}
    }
};
```

**JavaScript (journalPages.js) :**
```javascript
// Initialisation des traductions avec le Markdown brut
translations[lang] = {
    title: config.pageData.title?.[lang] || '',
    content: config.pageData.markdownContent?.[lang] || '',  // Markdown !
};
```

#### Avantages
- Données structurées et typées
- Pas de limite de taille
- Facile à déboguer (`console.log(pageConfig)`)
- Pas de problèmes d'échappement HTML

#### Inconvénients
- Variable globale (pollution du scope)
- Une seule entité à la fois

---

### Méthode 2 : Attributs data-* (News)

Utilisée pour les **news** car on affiche une liste avec plusieurs éléments.

#### Implémentation

**Template (journalNews.html.twig) :**
```html
<div class="news-item"
     data-news-id="{{ news.id }}"
     {% for lang in acceptedLanguages %}
         data-title-{{ lang }}="{{ news.title[lang]|e('html_attr') }}"
         data-content-{{ lang }}="{{ news.htmlContent[lang]|e('html_attr') }}"
         data-markdown-{{ lang }}="{{ news.content[lang]|e('html_attr') }}"
     {% endfor %}>
```

| Attribut | Contenu | Usage |
|----------|---------|-------|
| `data-content-xx` | HTML | Affichage dans la liste |
| `data-markdown-xx` | Markdown | Édition dans CKEditor |

**JavaScript (news.js) :**
```javascript
// Lors du clic sur "Edit"
config.acceptedLanguages.forEach(lang => {
    const markdownKey = `markdown${lang.charAt(0).toUpperCase() + lang.slice(1)}`;

    translations[lang] = {
        title: newsItem.dataset[titleKey] || '',
        content: newsItem.dataset[markdownKey] || '',  // Markdown !
    };
});
```

#### Avantages
- Données liées à chaque élément DOM
- Supporte les listes (plusieurs news)
- Pas de variable globale

#### Inconvénients
- Échappement HTML nécessaire (`|e('html_attr')`)
- Attributs volumineux si contenu long
- Plus difficile à déboguer

---

## Flux complet

### Pages

```
┌─────────────────────────────────────────────────────────────────────┐
│ 1. STOCKAGE (Base de données)                                       │
│    page.content = { "en": "![img](url){width=\"50%\"}", "fr": "..." }│
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 2. CONTROLLER (PageController.php)                                  │
│    $htmlContent = $markdownService->convertContentArray($content);  │
│    $markdownContent = $page->getContent();                          │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 3. TEMPLATE (journalPages.html.twig)                                │
│    window.pageConfig.pageData.content = HTML (affichage)            │
│    window.pageConfig.pageData.markdownContent = Markdown (édition)  │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 4. JAVASCRIPT (journalPages.js)                                     │
│    setEditorContent(markdownContent)  → CKEditor avec Markdown      │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 5. SAUVEGARDE                                                       │
│    getEditorContent() → Markdown avec attributs                     │
│    POST → Controller → Base de données                              │
└─────────────────────────────────────────────────────────────────────┘
```

### News

```
┌─────────────────────────────────────────────────────────────────────┐
│ 1. STOCKAGE (Base de données)                                       │
│    news.content = { "en": "![img](url){width=\"50%\"}", "fr": "..." }│
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 2. CONTROLLER (NewsController.php)                                  │
│    $htmlContent = $markdownService->convertContentArray($content);  │
│    news.htmlContent = HTML                                          │
│    news.content = Markdown (original)                               │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 3. TEMPLATE (journalNews.html.twig)                                 │
│    data-content-xx = HTML (affichage liste)                         │
│    data-markdown-xx = Markdown (édition)                            │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 4. JAVASCRIPT (news.js) - Clic sur "Edit"                           │
│    translations[lang].content = dataset.markdownXx                  │
│    setEditorContent(markdown) → CKEditor avec Markdown              │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 5. SAUVEGARDE                                                       │
│    getEditorContent() → Markdown avec attributs                     │
│    POST → Controller → Base de données                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Conversion Markdown ↔ HTML

### Service (MarkdownService.php)

```php
class MarkdownService
{
    public function __construct()
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());  // Important !

        $this->converter = new MarkdownConverter($environment);
    }

    public function toHtml(?string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }
}
```

### AttributesExtension

Cette extension permet de parser les attributs Markdown :

```markdown
![image](url){width="50%" style="float:left"}
```

Converti en :

```html
<img src="url" width="50%" style="float:left">
```

---

## CKEditor et Markdown

### Plugin Markdown GFM

CKEditor utilise le plugin `@ckeditor/ckeditor5-markdown-gfm` qui :
- **Charge** : Convertit Markdown → HTML interne
- **Sauvegarde** : Convertit HTML interne → Markdown

### Gestion des attributs d'image (ckeditor.js)

Le plugin Markdown standard ne supporte pas les attributs `{...}`. Notre code ajoute cette fonctionnalité :

**Sauvegarde :**
```javascript
function getEditorContent() {
    const imageInfo = getImageAttributesInfo();  // Récupère resize/align
    const markdown = editor.getData();
    return addImageAttributesToMarkdown(markdown, imageInfo);  // Ajoute {...}
}
```

**Chargement :**
```javascript
function setEditorContent(content) {
    const { cleanMarkdown, attributesMap } = parseImageAttributes(content);
    editor.setData(cleanMarkdown);
    applyImageAttributes(attributesMap);  // Applique resize/align
}
```

---

## Comparaison des méthodes

| Critère | Pages (window.config) | News (data-*) |
|---------|----------------------|---------------|
| Cas d'usage | Une entité | Liste d'entités |
| Taille contenu | Illimitée | Limitée (attributs HTML) |
| Débogage | Facile | Difficile |
| Scope | Global | Local à l'élément |
| Échappement | JSON | HTML attr |

## Fichiers impliqués

| Fichier | Rôle |
|---------|------|
| `src/Service/MarkdownService.php` | Conversion Markdown ↔ HTML |
| `src/Controller/PageController.php` | Préparation données pages |
| `src/Controller/NewsController.php` | Préparation données news |
| `templates/pages/journalPages.html.twig` | Template pages |
| `templates/news/journalNews.html.twig` | Template news |
| `assets/scripts/pages/journalPages.js` | JS pages |
| `assets/scripts/pages/news.js` | JS news |
| `assets/scripts/components/ckeditor.js` | Gestion CKEditor + attributs |

## Résumé

1. **Stockage** : Toujours en Markdown (avec attributs `{...}`)
2. **Affichage** : Converti en HTML par MarkdownService
3. **Édition** : Chargé en Markdown brut dans CKEditor
4. **Sauvegarde** : Markdown avec attributs préservés