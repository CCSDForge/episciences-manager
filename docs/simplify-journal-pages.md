# Plan : Simplifier Journal Pages (approche Twig comme News)

## Objectif

Remplacer l'approche AJAX complexe de Journal Pages par une approche Twig/formulaire simple comme News.

## Résumé des changements

| Avant (AJAX) | Après (Twig) |
|--------------|--------------|
| ~1400 lignes JS | ~300 lignes JS |
| Double réponse JSON/HTML | HTML uniquement |
| Édition inline | Formulaire collapsible |
| Navigation AJAX | Rechargement page |

---

## Phase 1 : Créer le template de formulaire

**Fichier à créer** : `/templates/pages/_page_form.html.twig`

Basé sur `/templates/news/_news_form.html.twig` :
- Formulaire collapsible avec Bootstrap
- Hidden inputs pour chaque langue (`translations[en][title]`, etc.)
- Intégration du language widget
- CKEditor pour le contenu
- Token CSRF

---

## Phase 2 : Modifier PageController.php

**Fichier** : `/src/Controller/PageController.php`

### Méthode `showPage()` (ligne 42-149)

- Supprimer le bloc `if ($request->isXmlHttpRequest())` (lignes 75-132)
- Toujours retourner un template HTML complet

### Méthode `editPage()` (ligne 151-253)

- Changer de `JsonResponse` à `Response` avec redirect
- Changer de `json_decode($request->getContent())` à `$request->request->all('translations')`
- Sauvegarder toutes les langues d'un coup
- Ajouter `addFlash('success', ...)` et `redirectToRoute()`

```php
// Nouveau pattern
$translations = $request->request->all('translations');
foreach ($translations as $lang => $data) {
    $currentTitle[$lang] = $data['title'];
    $currentContent[$lang] = $data['content'];
}

$page->setTitle($currentTitle);
$page->setContent($currentContent);
$entityManager->flush();

return $this->redirectToRoute('app_journal_page_view', [...]);
```

---

## Phase 3 : Modifier ReviewController.php

**Fichier** : `/src/Controller/ReviewController.php`

### Méthode `pageView()`

Passer les données complètes de la page au template :

```php
$currentPage = $pageRepository->findOneBy([...]);
$htmlContent = $markdownService->convertContentArray($currentPage?->getContent() ?? []);

return $this->render('review/journalPages.html.twig', [
    // ... params existants
    'currentPageData' => [
        'title' => $currentPage?->getTitle() ?? [],
        'content' => $htmlContent,
        'markdownContent' => $currentPage?->getContent() ?? [],
        'pageCode' => $pageCode,
    ],
]);
```

---

## Phase 4 : Modifier journalPages.html.twig

**Fichier** : `/templates/review/journalPages.html.twig`

### Changements :

1. **Ajouter l'inclusion du formulaire en haut** :
```twig
{% include 'pages/_page_form.html.twig' with {...} %}
```

2. **Changer les liens sidebar** de `href="#"` avec `data-page-code` à :
```twig
href="{{ path('app_journal_page_view', {'code': code, 'pageCode': page.pageCode}) }}"
```

3. **Rendre le contenu côté serveur** (pas JS) :
```twig
<h1>{{ currentPageData.title[app.request.locale]|default(...) }}</h1>
<div>{{ currentPageData.content[app.request.locale]|default(...)|raw }}</div>
```

4. **Supprimer le bloc** `window.journalPagesData = {...}` (lignes 335-364)

5. **Supprimer les divs d'édition inline** (`#inline-edit-content`, etc.)

---

## Phase 5 : Simplifier journalPages.js

**Fichier** : `/assets/scripts/pages/journalPages.js`

### À supprimer (~1100 lignes) :

- AJAX page loading (`fetch()` pour charger les pages)
- AJAX save handler (JSON save)
- Fonctions inline edit (`switchToInlineEdit()`, `exitInlineEdit()`)
- Language switching AJAX
- Dynamic breadcrumb updates

### À garder (~300 lignes) :

- Language widget pour le formulaire
- CKEditor initialization
- Mobile menu toggle
- Form validation avant submit
- Sidebar collapse (Bootstrap natif)

---

## Phase 6 : Nettoyer les styles SCSS

**Fichier** : `/assets/styles/pages/journalPages.scss`

### À supprimer :

- Styles `#inline-edit-content`
- Styles `#page-content-inline`

### À ajouter (copier de news.scss) :

- Styles pour `.page-form-collapse`
- Styles pour `.page-form-card`

---

## Routes (compatibilité maintenue)

| Route | Méthode | Changement |
|-------|---------|------------|
| `/journal/{code}/pages` | GET | Aucun |
| `/journal/{code}/pages/{pageCode}` | GET | Rend le contenu côté serveur |
| `/journal/{code}/page/{pageTitle}` | GET | **Supprimer** (plus de endpoint AJAX) |
| `/journal/{code}/page/{pageTitle}/edit` | POST | Form POST au lieu de JSON |

---

## Ordre d'implémentation

1. `templates/pages/_page_form.html.twig` (créer)
2. `src/Controller/PageController.php` (modifier editPage)
3. `src/Controller/ReviewController.php` (passer currentPageData)
4. `templates/review/journalPages.html.twig` (refactoring majeur)
5. `assets/scripts/pages/journalPages.js` (simplifier)
6. `assets/styles/pages/journalPages.scss` (nettoyer)

---

## Vérification

1. **Navigation** : Cliquer sur une page → rechargement complet, contenu correct
2. **Édition** : Cliquer Edit → formulaire s'ouvre → sauvegarder → redirect avec flash
3. **Langues** : Changer de langue dans le widget → contenu du formulaire change
4. **Mobile** : Sidebar slide-in fonctionne toujours
5. **Console** : Aucun appel AJAX pour les pages

---

## Fichiers critiques

- `/src/Controller/PageController.php`
- `/src/Controller/ReviewController.php`
- `/templates/review/journalPages.html.twig`
- `/templates/pages/_page_form.html.twig` (nouveau)
- `/assets/scripts/pages/journalPages.js`