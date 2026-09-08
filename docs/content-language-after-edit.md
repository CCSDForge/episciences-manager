# Content Language After Edit

Documentation de la fonctionnalité qui affiche automatiquement le contenu dans la langue qui vient d'être éditée.

## Problème

Avant cette modification :
- L'utilisateur édite du contenu en **espagnol** (avec interface en anglais)
- Après sauvegarde, la page affiche le contenu en **anglais** (langue de l'interface)
- L'utilisateur ne voit pas directement le résultat de son édition

## Solution

Après sauvegarde, le contenu est affiché dans la langue qui vient d'être éditée, indépendamment de la langue de l'interface.

### Flux

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. Édition                                                      │
│    - Interface: anglais                                         │
│    - Contenu édité: espagnol                                    │
│    - Champ hidden "language" = "es"                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. POST /journal/{code}/pages/{pageCode}/edit                   │
│    - Sauvegarde du contenu                                      │
│    - Session: content_language = "es"                           │
│    - Redirect vers page view                                    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. GET /journal/{code}/pages/{pageCode}                         │
│    - Récupère content_language = "es" de la session             │
│    - Supprime la valeur de la session (usage unique)            │
│    - Passe contentLanguage au template                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. JavaScript (journalPages.js)                                 │
│    - config.contentLanguage = "es"                              │
│    - currentLang = "es" (priorité sur locale interface)         │
│    - Widget sélectionne "es"                                    │
│    - Contenu espagnol affiché                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Implémentation

### 1. Contrôleur (`PageController.php`)

#### Sauvegarde de la langue éditée (méthode `edit`)

```php
try {
    $entityManager->flush();
    $this->addFlash('success', $translator->trans('journalPages.flash.saved'));

    // Store the edited language to display content in that language after redirect
    $editedLanguage = $request->request->get('language');
    if ($editedLanguage) {
        $request->getSession()->set('content_language', $editedLanguage);
    }
} catch (\Throwable $e) {
    // ...
}
```

#### Récupération de la langue (méthode `pageView`)

```php
// Check if we should display content in a specific language (after edit)
$contentLanguage = $request->getSession()->get('content_language');
if ($contentLanguage) {
    // Clear it so it's only used once
    $request->getSession()->remove('content_language');
}

return $this->render('pages/journalPages.html.twig', [
    // ...
    'contentLanguage' => $contentLanguage,
]);
```

### 2. Template (`journalPages.html.twig`)

```twig
window.pageConfig = {
    locale: {{ app.request.locale|json_encode|raw }},
    // ...
    contentLanguage: {{ contentLanguage|default(null)|json_encode|raw }},
    // ...
};
```

### 3. JavaScript (`journalPages.js`)

```javascript
// Initialize with contentLanguage (after edit) or interface locale
let currentLang = config.contentLanguage && config.acceptedLanguages.includes(config.contentLanguage)
    ? config.contentLanguage
    : config.acceptedLanguages.includes(config.locale)
        ? config.locale
        : config.defaultLanguage;
```

## Priorité des langues

L'ordre de priorité pour `currentLang` est :

1. **`contentLanguage`** - Langue du contenu qui vient d'être édité (si définie et valide)
2. **`locale`** - Langue de l'interface (si elle fait partie des langues acceptées)
3. **`defaultLanguage`** - Langue par défaut de la revue

## Notes importantes

- **Usage unique** : `content_language` est supprimée de la session après lecture pour éviter de persister indéfiniment
- **Validation** : La langue est vérifiée contre `acceptedLanguages` pour éviter les valeurs invalides
- **URL inchangée** : L'URL reste la même, seul le contenu affiché change
- **Interface préservée** : La langue de l'interface (menus, boutons) reste inchangée

## Fichiers modifiés

| Fichier | Modification |
|---------|--------------|
| `src/Controller/PageController.php` | Stockage et récupération de `content_language` dans la session |
| `templates/pages/journalPages.html.twig` | Passage de `contentLanguage` à la config JavaScript |
| `assets/scripts/pages/journalPages.js` | Utilisation de `contentLanguage` pour initialiser `currentLang` |