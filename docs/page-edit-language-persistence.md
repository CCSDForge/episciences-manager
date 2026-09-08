# Persistance de la langue lors de l'édition des pages

## Problème

Lors de l'édition des pages multilingues, la langue sélectionnée n'était pas toujours préservée. Par exemple :

1. L'utilisateur édite une page en **espagnol** et sauvegarde
2. Après la sauvegarde, la page s'affiche en espagnol (OK)
3. L'utilisateur clique sur le crayon pour modifier à nouveau
4. **Bug** : La page d'édition s'ouvre parfois en **anglais** au lieu de l'espagnol

## Cause du problème

### Ancien mécanisme : sessionStorage

L'ancien code utilisait `sessionStorage` pour stocker la langue cliquée :

```javascript
// Lors du clic sur le crayon
sessionStorage.setItem('editContentLanguage', lang);
window.location.href = `/journal/.../edit`;

// Sur la page d'édition
const editContentLanguage = sessionStorage.getItem('editContentLanguage');
```

### Pourquoi sessionStorage n'est pas fiable

1. **Paramètres de confidentialité** : Certains navigateurs bloquent sessionStorage
2. **Mode privé** : sessionStorage peut être désactivé ou effacé
3. **Entre onglets** : sessionStorage n'est pas partagé entre onglets
4. **Timing** : Peut être effacé entre la redirection et la lecture

Quand `sessionStorage` échoue, le code utilisait la langue par défaut (souvent anglais).

### Deuxième problème : URL non mise à jour

Même après correction, un autre problème existait :

1. L'utilisateur ouvre l'édition avec `?contentLang=es`
2. Il clique sur le crayon pour le français
3. L'éditeur passe au français, mais l'URL reste `?contentLang=es`
4. Si l'utilisateur rafraîchit la page, il revient à l'espagnol

## Solution implémentée

### 1. Paramètre URL au lieu de sessionStorage

La langue est passée directement dans l'URL :

```javascript
// Avant (non fiable)
sessionStorage.setItem('editContentLanguage', lang);
const editUrl = `/${locale}/journal/${code}/pages/${pageCode}/edit`;

// Après (fiable)
const editUrl = `/${locale}/journal/${code}/pages/${pageCode}/edit?contentLang=${lang}`;
```

### 2. Lecture du paramètre URL

```javascript
const urlParams = new URLSearchParams(window.location.search);
const urlContentLang = urlParams.get('contentLang');
```

### 3. Mise à jour dynamique de l'URL

Quand l'utilisateur change de langue sans recharger la page :

```javascript
function updateUrlContentLang(lang) {
  const url = new URL(window.location.href);
  url.searchParams.set('contentLang', lang);
  window.history.replaceState({}, '', url.toString());
}
```

## Priorité des langues

Le code détermine la langue à afficher selon cette priorité :

| Priorité | Source | Description |
|----------|--------|-------------|
| 1 | `?contentLang=` | Paramètre URL (clic sur crayon) |
| 2 | `sessionStorage` | Compatibilité avec ancien code |
| 3 | `contentLanguage` | Session PHP (après sauvegarde) |
| 4 | `locale` | Langue de l'interface |
| 5 | `defaultLanguage` | Langue par défaut du journal |

```javascript
let currentLang =
  urlContentLang && acceptedLanguages.includes(urlContentLang)
    ? urlContentLang
    : sessionContentLang && acceptedLanguages.includes(sessionContentLang)
      ? sessionContentLang
      : config.contentLanguage && acceptedLanguages.includes(config.contentLanguage)
        ? config.contentLanguage
        : acceptedLanguages.includes(config.locale)
          ? config.locale
          : config.defaultLanguage;
```

## Flux complet

### Clic sur le crayon (mode vue)

```
Utilisateur clique crayon "ES"
         ↓
handleTranslationClick('es')
         ↓
Redirection vers /edit?contentLang=es
         ↓
Page d'édition charge
         ↓
urlParams.get('contentLang') → 'es'
         ↓
currentLang = 'es'
         ↓
Éditeur affiche contenu espagnol
```

### Changement de langue (mode édition)

```
Utilisateur en mode édition (ES)
         ↓
Clique crayon "FR"
         ↓
handleTranslationClick('fr')
         ↓
saveCurrentLanguage() - sauvegarde ES
         ↓
loadLanguage('fr') - charge FR
         ↓
updateUrlContentLang('fr')
         ↓
URL devient ?contentLang=fr (sans rechargement)
```

### Après sauvegarde

```
POST /edit avec language='es'
         ↓
Controller stocke en session PHP
$session->set('content_language', 'es')
         ↓
Redirect vers /view
         ↓
Controller lit session et passe au template
contentLanguage: 'es'
         ↓
JavaScript utilise config.contentLanguage
         ↓
Page affiche contenu espagnol
```

## Fichiers modifiés

| Fichier | Modification |
|---------|--------------|
| `assets/scripts/pages/journalPages.js` | Lecture paramètre URL, mise à jour dynamique URL |

## Avantages de la solution

1. **Fiabilité** : L'URL ne peut pas être "perdue" ou "bloquée"
2. **Partageabilité** : On peut partager un lien direct vers l'édition d'une langue
3. **Rafraîchissement** : F5 garde la bonne langue
4. **Débogage** : Facile de voir quelle langue est active dans l'URL
5. **Compatibilité** : sessionStorage reste comme fallback

## Limitations

1. L'URL est légèrement plus longue avec le paramètre
2. Le paramètre reste visible dans l'URL (pas un problème en pratique)