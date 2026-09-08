# Fix: Show Correct Language When Clicking Translation Pencil

Documentation du fix pour afficher la bonne langue quand on clique sur le crayon de traduction dans le widget de langue.

## Problème

Avant ce fix :
- L'utilisateur est sur la page de vue (interface en anglais)
- Il clique sur le crayon **Fr** pour éditer le contenu français
- La page d'édition s'ouvre mais affiche le contenu en **anglais**
- Il faut cliquer une deuxième fois sur Fr pour voir le français

## Cause

Sur la page de vue (`editMode=false`), le formulaire d'édition n'existe pas. Quand on clique sur le crayon :

```javascript
function handleTranslationClick(lang) {
  // ...
  } else if (config.currentPage) {
    // View mode: form not in DOM, redirect to edit page
    const editUrl = `/${config.locale}/journal/.../edit`;
    window.location.href = editUrl;  // ← Redirige avec la locale d'interface (en)
  }
}
```

La redirection utilisait `config.locale` (langue de l'interface) au lieu de `lang` (langue cliquée).

## Solution

Utiliser `sessionStorage` pour passer la langue cliquée entre les pages :

### 1. Stocker la langue avant redirection

```javascript
} else if (config.currentPage) {
  // Store the clicked language in session so edit page shows correct content
  sessionStorage.setItem('editContentLanguage', lang);
  const editUrl = `/${config.locale}/journal/${config.journalCode}/pages/${config.currentPage}/edit`;
  window.location.href = editUrl;
}
```

### 2. Récupérer la langue au chargement

```javascript
// Check if we came from clicking a translation pencil (stored in sessionStorage)
const editContentLanguage = sessionStorage.getItem('editContentLanguage');
if (editContentLanguage) {
  sessionStorage.removeItem('editContentLanguage'); // Use only once
}

// Initialize with: editContentLanguage (from pencil click) > contentLanguage (after save) > interface locale
let currentLang =
  editContentLanguage && config.acceptedLanguages.includes(editContentLanguage)
    ? editContentLanguage
    : config.contentLanguage && config.acceptedLanguages.includes(config.contentLanguage)
      ? config.contentLanguage
      : config.acceptedLanguages.includes(config.locale)
        ? config.locale
        : config.defaultLanguage;
```

## Priorité des langues

L'ordre de priorité pour `currentLang` est maintenant :

1. **`editContentLanguage`** - Langue du crayon cliqué (via `sessionStorage`)
2. **`contentLanguage`** - Langue après sauvegarde (via session PHP)
3. **`locale`** - Langue de l'interface
4. **`defaultLanguage`** - Langue par défaut de la revue

## Flux complet

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. Page de vue (interface en anglais)                           │
│    - Utilisateur clique sur crayon "Fr"                         │
│    - handleTranslationClick('fr') appelé                        │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. Stockage et redirection                                      │
│    - sessionStorage.setItem('editContentLanguage', 'fr')        │
│    - Redirect vers /en/journal/.../edit                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. Page d'édition                                               │
│    - sessionStorage.getItem('editContentLanguage') → 'fr'       │
│    - sessionStorage.removeItem('editContentLanguage')           │
│    - currentLang = 'fr'                                         │
│    - Formulaire affiche le contenu français                     │
└─────────────────────────────────────────────────────────────────┘
```

## Nettoyage de code

Le code suivant a été supprimé car jamais exécuté :

```javascript
// SUPPRIMÉ - Le formulaire n'est jamais un collapse en mode édition
pageEditForm.addEventListener('shown.bs.collapse', async () => {
  // ...
});

pageEditForm.addEventListener('hidden.bs.collapse', () => {
  // ...
});
```

**Raison :** En mode vue, le formulaire n'existe pas. En mode édition, le formulaire existe mais n'a pas la classe `collapse` (il est toujours visible).

## Fichiers modifiés

| Fichier | Modification |
|---------|--------------|
| `assets/scripts/pages/journalPages.js` | Ajout `sessionStorage` pour passer la langue, nettoyage code inutilisé |

## Tests

1. Aller sur la page de vue d'une page (interface en anglais)
2. Cliquer sur le crayon "Fr" dans le widget de langue
3. Vérifier que la page d'édition affiche le contenu français
4. Vérifier que le dropdown du widget est sur "Fr"