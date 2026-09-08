# Gestion des langues : Pages vs News

Cette documentation explique la logique entre le widget de langue et l'édition de page, ainsi que les différences avec la page News.

## Vue d'ensemble

Les deux systèmes (Pages et News) utilisent le **même composant partagé** `initLanguageWidget()` situé dans `assets/scripts/components/language-widget.js`, mais avec des configurations différentes adaptées à leurs besoins respectifs.

---

## 1. Pages - Logique du widget de langue

### Fichiers concernés

- `assets/scripts/pages/journalPages.js`
- `templates/review/journalPages.html.twig`
- `templates/pages/_page_form.html.twig`
- `src/Service/PageHierarchyService.php`

### Source des données

| Champ | Source | Éditable |
|-------|--------|----------|
| **Titre** | YAML (`pages_hierarchy.yaml`) | Non (lecture seule) |
| **Contenu** | Base de données | Oui |

Les titres des pages sont définis dans la configuration YAML car ils font partie de la structure hiérarchique du site et ne doivent pas être modifiés par les utilisateurs.

### Position du widget

Le widget de langue est situé dans la **sidebar** (barre latérale), toujours visible.

### Comportement contextuel du widget

Le widget de la sidebar a un comportement qui dépend de l'état du formulaire :

```
┌─────────────────────────────────────────────────────────┐
│                  FORMULAIRE OUVERT                       │
├─────────────────────────────────────────────────────────┤
│  Clic sur une langue →                                  │
│    1. Sauvegarde la langue courante                     │
│    2. Charge la nouvelle langue dans le formulaire      │
│    3. Reste sur la même page                            │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│                  FORMULAIRE FERMÉ                        │
├─────────────────────────────────────────────────────────┤
│  Clic sur une langue →                                  │
│    Redirige vers une autre URL pour changer             │
│    la langue de l'interface                             │
│    Ex: /fr/pages → /en/pages                            │
└─────────────────────────────────────────────────────────┘
```

### Logique des icônes

```javascript
// Pages utilise: iconBasedOnContentOnly = true
// L'icône crayon (✏️) s'affiche UNIQUEMENT si du contenu existe
// Les titres YAML ne comptent pas car ils ne sont pas éditables
const showPencil = iconBasedOnContentOnly ? hasContent : hasContent || hasTitle;
```

**Pourquoi ?** Montrer un crayon basé sur le titre YAML serait trompeur puisque le titre n'est pas éditable. On ne montre le crayon que si du contenu utilisateur existe.

### Flux de données

```
YAML (titres) + BDD (contenu)
       ↓
PageHierarchyService.organizePages()
       ↓
window.pageConfig.pageData (JSON passé au template)
       ↓
journalPages.js lit les données
       ↓
Formulaire → hidden inputs → Soumission
       ↓
Serveur reçoit translations[lang][title] + translations[lang][content]
```

---

## 2. News - Logique du widget de langue

### Fichiers concernés

- `assets/scripts/pages/news.js`
- `templates/news/_news_form.html.twig`
- `templates/review/journalNews.html.twig`

### Source des données

| Champ | Source | Éditable |
|-------|--------|----------|
| **Titre** | Base de données | Oui |
| **Contenu** | Base de données | Oui |
| **Lien** | Base de données | Oui |

Tout est stocké en base de données et éditable par l'utilisateur.

### Position du widget

Le widget de langue est **intégré dans le formulaire** (pas dans la sidebar).

### Comportement du widget

Le comportement est plus simple car le widget n'est visible que quand le formulaire est ouvert :

```
┌─────────────────────────────────────────────────────────┐
│              CHANGEMENT DE LANGUE                        │
├─────────────────────────────────────────────────────────┤
│  Clic sur une langue →                                  │
│    1. Sauvegarde la langue courante                     │
│    2. Charge la nouvelle langue                         │
│    3. Pas de redirection                                │
└─────────────────────────────────────────────────────────┘
```

### Logique des icônes

```javascript
// News utilise: iconBasedOnContentOnly = false (défaut)
// L'icône crayon (✏️) s'affiche si titre OU contenu existe
// Tout est éditable par l'utilisateur
const showPencil = hasContent || hasTitle;
```

### Deux modes de fonctionnement

News possède des modes création et édition séparés :

- **Mode Création** : Le formulaire se réinitialise à la fermeture
- **Mode Édition** : Les données sont chargées depuis les attributs `data-*` de l'élément news

### Flux de données

```
BDD (tout)
       ↓
Template → data-* attributes sur les éléments DOM
       ↓
Clic "éditer" → news.js lit les data-*
       ↓
Formulaire → hidden inputs → Soumission
       ↓
Serveur reçoit translations[lang][title] + translations[lang][content] + translations[lang][link]
```

---

## 3. Tableau comparatif

| Aspect | Pages | News |
|--------|-------|------|
| **Source du titre** | YAML (lecture seule) | Base de données (éditable) |
| **Position du widget** | Sidebar (toujours visible) | Dans le formulaire |
| **Champs par langue** | titre, contenu | titre, contenu, lien |
| **Option iconBasedOnContentOnly** | `true` | `false` |
| **Form fermé + clic langue** | Redirige (change interface) | N/A |
| **Form ouvert + clic langue** | Change dans le form | Change dans le form |
| **Mode création/édition** | Toujours édition | Modes séparés |
| **Reset à la fermeture** | Détruit l'éditeur seulement | Reset tous les champs |
| **Validation CKEditor** | Aucune | Limite de caractères (5000) |
| **Champ statut** | N/A | Select (public/invisible) |

---

## 4. Structure des hidden inputs

Les deux systèmes utilisent le même pattern de nommage pour les inputs cachés :

```html
{% for lang in acceptedLanguages %}
  <!-- Pages -->
  <input type="hidden" name="translations[{{ lang }}][title]" id="translation-title-{{ lang }}">
  <input type="hidden" name="translations[{{ lang }}][content]" id="translation-content-{{ lang }}">

  <!-- News (ajoute link) -->
  <input type="hidden" name="translations[{{ lang }}][title]" id="translation-title-{{ lang }}">
  <input type="hidden" name="translations[{{ lang }}][content]" id="translation-content-{{ lang }}">
  <input type="hidden" name="translations[{{ lang }}][link]" id="translation-link-{{ lang }}">
{% endfor %}
```

Le serveur reçoit les données dans le contrôleur via :
```php
$translations = $request->request->all()['translations'];
```

---

## 5. Composant partagé : initLanguageWidget()

Le composant `initLanguageWidget()` dans `assets/scripts/components/language-widget.js` accepte plusieurs options de configuration :

```javascript
initLanguageWidget({
  widgetId: 'sidebar',           // Identifiant unique du widget
  acceptedLanguages: ['fr', 'en'], // Langues disponibles
  selectedLanguage: 'fr',        // Langue sélectionnée par défaut
  titleByLocale: {},             // Titres par langue
  contentByLocale: {},           // Contenus par langue
  linkByLocale: {},              // Liens par langue (optionnel)
  iconBasedOnContentOnly: true,  // true pour Pages, false pour News
  onLanguageChange: (lang) => {}, // Callback au changement de langue
  onTranslationClick: (lang) => {} // Callback au clic sur une traduction
});
```

### Méthodes disponibles

- `updateTranslations(titleByLocale, contentByLocale, linkByLocale)` : Met à jour les icônes
- `updateOptions(contentByLocale)` : Met à jour les options du dropdown
- `getSelectedLanguage()` : Retourne la langue sélectionnée

---

## 6. Schéma récapitulatif

```
┌─────────────────────────────────────────────────────────────────────┐
│                              PAGES                                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│   ┌──────────────┐     ┌─────────────────────────────────────┐      │
│   │   SIDEBAR    │     │           FORMULAIRE                │      │
│   │              │     │                                     │      │
│   │  [FR] ✏️     │────▶│  Titre: [Lecture seule - YAML]     │      │
│   │  [EN] +      │     │  Contenu: [CKEditor éditable]      │      │
│   │              │     │                                     │      │
│   └──────────────┘     └─────────────────────────────────────┘      │
│         │                                                            │
│         │ (form fermé)                                               │
│         ▼                                                            │
│   Redirection vers /en/pages (change interface)                      │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                              NEWS                                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│   ┌─────────────────────────────────────────────────────────────┐   │
│   │                    FORMULAIRE                                │   │
│   │                                                              │   │
│   │  ┌─────────────────────────┐    ┌────────────────────────┐  │   │
│   │  │ Titre: [Éditable]       │    │  WIDGET LANGUE         │  │   │
│   │  │ Contenu: [CKEditor]     │    │                        │  │   │
│   │  │ Lien: [Éditable]        │    │  [FR] ✏️   [EN] +      │  │   │
│   │  │ Statut: [Select]        │    │                        │  │   │
│   │  └─────────────────────────┘    └────────────────────────┘  │   │
│   │                                                              │   │
│   └─────────────────────────────────────────────────────────────┘   │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 7. Pourquoi ces différences ?

1. **Titres des pages en YAML** : Les titres de pages sont définis dans la configuration car ils font partie de la structure hiérarchique du site. Ils ne doivent pas être modifiés librement par les utilisateurs.

2. **Widget dans la sidebar (Pages)** : Permet de changer la langue de l'interface quand le formulaire est fermé, offrant une double fonctionnalité navigation/édition.

3. **Widget dans le formulaire (News)** : Les news n'ont pas besoin de changer la langue de l'interface, seulement de gérer les traductions du contenu.

4. **`iconBasedOnContentOnly: true` (Pages)** : Évite d'afficher un crayon trompeur basé sur les titres YAML non éditables.

5. **Modes création/édition séparés (News)** : Les news peuvent être créées et éditées, contrairement aux pages qui existent déjà dans la hiérarchie YAML.