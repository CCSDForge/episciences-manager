# Fallback de contenu vers la langue par défaut

## Objectif

Quand une page n'a pas de contenu pour une langue sélectionnée, afficher automatiquement le contenu de la langue par défaut du journal (définie dans les settings).

## Configuration

La langue par défaut est définie dans les settings du journal :

```json
{
  "languages": {
    "default": "en",
    "accepted": ["en", "fr", "es"]
  }
}
```

## Comportement

### Mode visualisation (formulaire fermé)

| Action | Résultat |
|--------|----------|
| Chargement de la page | Le widget sélectionne `defaultLanguage`, affiche le contenu de `defaultLanguage` |
| Changer le widget vers une langue avec contenu | Affiche le contenu de cette langue |
| Changer le widget vers une langue sans contenu | Affiche le contenu de `defaultLanguage` (fallback) |

### Mode édition (formulaire ouvert)

| Action | Résultat |
|--------|----------|
| Ouvrir l'éditeur | Charge le contenu de la langue sélectionnée dans le widget |
| Changer le widget vers une langue sans contenu | L'éditeur affiche le contenu de `defaultLanguage` comme point de départ |

## Distinction importante

- **Langue d'interface** (`app.request.locale`) : La langue de l'URL (`/fr/...` ou `/en/...`). Contrôle la traduction de l'interface utilisateur (boutons, labels, menus).
- **Langue du widget** : La langue sélectionnée dans le dropdown du widget. Contrôle quel contenu de page est affiché/édité.

Ces deux langues sont **indépendantes**. Changer la langue du widget ne change pas l'URL ni la langue d'interface.

## Fichiers modifiés

### 1. `templates/components/language_widget.html.twig`

Le dropdown sélectionne la langue par défaut du journal au chargement (pas la langue d'interface).

```twig
<!-- Le dropdown sélectionne defaultLanguage -->
<option value="{{ lang }}" {{ defaultLanguage == lang ? 'selected' : '' }}>
```

### 2. `templates/review/journalPages.html.twig`

#### IDs pour mise à jour JavaScript
```twig
<div id="page-view-title" class="form-control bg-light">
<div id="page-view-content" class="ck-content ...">
```

#### Contenu initial basé sur defaultLanguage
```twig
{% set defaultContent = currentPageData.content[defaultLanguage]|default('') %}
{% if defaultContent is not empty %}
    {{ defaultContent|raw }}
{% else %}
    <p class="text-muted fst-italic no-content-message">{{ 'journalPages.noContentAvailable'|trans }}</p>
{% endif %}
```

#### Traductions pour JavaScript
```twig
window.pageConfig = {
  // ...
  translations: {
    noContentAvailable: {{ 'journalPages.noContentAvailable'|trans|json_encode|raw }}
  }
};
```

### 3. `assets/scripts/pages/journalPages.js`

#### Fonction `updateViewContent()`

Met à jour le contenu affiché en mode visualisation (sans redirection).

```javascript
function updateViewContent(lang) {
  const titleElement = document.getElementById('page-view-title');
  const contentElement = document.getElementById('page-view-content');

  if (!config.pageData || !titleElement || !contentElement) return;

  // Fallback vers defaultLanguage si pas de contenu
  const title = config.pageData.title?.[lang] || config.pageData.title?.[config.defaultLanguage] || '';
  const content = config.pageData.content?.[lang] || config.pageData.content?.[config.defaultLanguage] || '';

  if (title) {
    titleElement.textContent = title;
  }

  if (content) {
    contentElement.innerHTML = content;
  } else {
    const noContentMsg = config.translations?.noContentAvailable || 'No content available';
    contentElement.innerHTML = `<p class="text-muted fst-italic no-content-message">${noContentMsg}</p>`;
  }
}
```

#### Fonction `handleLanguageChange()`

Gère le changement de langue dans le widget.

```javascript
function handleLanguageChange(selectedLang) {
  if (formIsOpen) {
    // Mode édition : change le contenu de l'éditeur
    saveCurrentLanguage();
    loadLanguage(selectedLang);
  } else {
    // Mode visualisation : met à jour le contenu affiché (pas de redirection)
    updateViewContent(selectedLang);
  }
  currentLang = selectedLang;
}
```

#### Fonction `loadLanguage()`

Charge le contenu dans l'éditeur avec fallback.

```javascript
function loadLanguage(lang) {
  currentLang = lang;
  const data = translations[lang] || { title: '', content: '' };
  const defaultData = translations[config.defaultLanguage] || { title: '', content: '' };

  // ...

  if (editorInitialized) {
    // Fallback vers defaultLanguage si contenu vide
    const contentToLoad = data.content || defaultData.content || '';
    setEditorContent(contentToLoad);
  }
}
```

## Exemple d'utilisation

### Scénario
- Journal "epijinfo" avec `defaultLanguage: "en"`
- Page "for-reviewers-test" avec contenu anglais, sans contenu français

### Résultat attendu
1. L'utilisateur charge la page → Widget affiche "English", contenu anglais affiché
2. L'utilisateur sélectionne "Français" dans le widget → Contenu anglais toujours affiché (fallback)
3. L'utilisateur ouvre l'éditeur → Contenu anglais chargé comme point de départ pour la traduction française
4. L'utilisateur écrit du contenu français et sauvegarde → Contenu français maintenant disponible
5. La prochaine fois que "Français" est sélectionné → Contenu français affiché



Le fichier contient :
- Objectif : Remplacer l'approche AJAX par Twig/formulaire
- Résumé : Tableau comparatif avant/après
- 6 phases détaillées avec les fichiers et changements
- Routes : Tableau de compatibilité
- Ordre d'implémentation : 1 à 6
- Vérification : 5 points de test
- Fichiers critiques : Liste des fichiers concernés    
