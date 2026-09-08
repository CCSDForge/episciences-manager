# Guide Stimulus.js

## Introduction

Stimulus est un framework JavaScript léger créé par Basecamp (créateurs de Ruby on Rails). Il fait partie de la suite **Hotwire** avec Turbo, mais peut être utilisé indépendamment.

### Philosophie

- HTML comme source de vérité
- JavaScript pour améliorer le HTML existant (pas le remplacer)
- Pas de rendu côté client (contrairement à React/Vue)
- Fonctionne avec le HTML rendu par le serveur (Twig)

## Installation dans le projet

Le projet est déjà configuré pour Stimulus :

```
assets/
├── bootstrap.js          # Initialise Stimulus
├── controllers.json      # Registre des contrôleurs
└── controllers/
    └── hello_controller.js  # Exemple par défaut
```

**Dépendances (package.json) :**
- `@hotwired/stimulus: ^3.2.2`
- `@symfony/stimulus-bridge: ^4.0.1`

**Webpack (webpack.config.js) :**
```javascript
.enableStimulusBridge('./assets/controllers.json')
```

## Les 3 concepts clés

### 1. Controllers

Un contrôleur est une classe JS qui gère un élément HTML.

```javascript
// assets/controllers/example_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    // Appelé quand l'élément apparaît dans le DOM
    console.log('Contrôleur connecté!');
  }

  disconnect() {
    // Appelé quand l'élément est retiré du DOM
  }
}
```

**Convention de nommage :**
- Fichier : `example_controller.js` ou `example-controller.js`
- Utilisation : `data-controller="example"`

### 2. Targets

Les targets remplacent les sélecteurs CSS (`querySelector`).

```javascript
// Contrôleur
export default class extends Controller {
  static targets = ['input', 'output'];

  greet() {
    this.outputTarget.textContent = `Bonjour ${this.inputTarget.value}!`;
  }
}
```

```twig
{# Template Twig #}
<div data-controller="greeter">
    <input data-greeter-target="input" type="text">
    <button data-action="click->greeter#greet">Saluer</button>
    <span data-greeter-target="output"></span>
</div>
```

**API des targets :**
| Propriété | Description |
|-----------|-------------|
| `this.inputTarget` | Premier élément avec ce target |
| `this.inputTargets` | Tableau de tous les éléments |
| `this.hasInputTarget` | Boolean : le target existe-t-il ? |

### 3. Actions

Les actions lient les événements DOM aux méthodes du contrôleur.

**Syntaxe :**
```
data-action="[événement]->[contrôleur]#[méthode]"
```

**Exemples :**
```twig
{# Clic sur un bouton #}
<button data-action="click->example#submit">Envoyer</button>

{# Changement de select #}
<select data-action="change->example#filter">

{# Frappe clavier #}
<input data-action="input->search#query">

{# Soumission de formulaire #}
<form data-action="submit->form#save">

{# Plusieurs actions #}
<input data-action="input->search#query keydown.enter->search#submit">
```

**Événements par défaut :**
Si vous omettez l'événement, Stimulus utilise l'événement par défaut de l'élément :
- `<button>` → click
- `<input>` → input
- `<form>` → submit
- `<select>` → change

```twig
{# Ces deux lignes sont équivalentes #}
<button data-action="click->example#submit">
<button data-action="example#submit">
```

## Cycle de vie

```javascript
export default class extends Controller {
  initialize() {
    // Une seule fois, au chargement
  }

  connect() {
    // Chaque fois que l'élément entre dans le DOM
  }

  disconnect() {
    // Chaque fois que l'élément sort du DOM
  }
}
```

## Values (paramètres configurables)

Les values permettent de passer des données du HTML au JS.

```javascript
export default class extends Controller {
  static values = {
    url: String,
    refreshInterval: { type: Number, default: 5000 },
    autoStart: Boolean
  };

  connect() {
    console.log(this.urlValue);           // String
    console.log(this.refreshIntervalValue); // Number (5000 par défaut)
    console.log(this.autoStartValue);      // Boolean
  }
}
```

```twig
<div data-controller="loader"
     data-loader-url-value="/api/data"
     data-loader-refresh-interval-value="3000"
     data-loader-auto-start-value="true">
</div>
```

## Exemple complet : Language Widget

Voici comment le language widget pourrait être converti en Stimulus :

### Contrôleur JS

```javascript
// assets/controllers/language_widget_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['select', 'translationRow', 'titleInput'];

  static values = {
    currentLang: String,
    acceptedLanguages: Array
  };

  connect() {
    this.currentLangValue = this.selectTarget.value;
  }

  switchLanguage(event) {
    const newLang = event.target.value;
    this.currentLangValue = newLang;

    // Dispatch un événement custom pour les autres composants
    this.dispatch('languageChanged', { detail: { language: newLang } });
  }

  editTranslation(event) {
    const lang = event.currentTarget.closest('[data-lang]').dataset.lang;
    this.dispatch('translationEdit', { detail: { language: lang } });
  }

  updateTranslationsList(contentByLocale) {
    this.translationRowTargets.forEach(row => {
      const lang = row.dataset.lang;
      const hasContent = contentByLocale[lang]?.trim().length > 0;
      const icon = row.querySelector('i');

      if (hasContent) {
        icon.className = 'fas fa-pencil-alt text-primary';
      } else {
        icon.className = 'fas fa-plus text-muted';
      }
    });
  }
}
```

### Template Twig

```twig
<div class="language-widget"
     data-controller="language-widget"
     data-language-widget-current-lang-value="{{ defaultLanguage }}"
     data-language-widget-accepted-languages-value="{{ acceptedLanguages|json_encode }}">

    <select data-language-widget-target="select"
            data-action="change->language-widget#switchLanguage">
        {% for lang in acceptedLanguages %}
            <option value="{{ lang }}">{{ lang|language_name(lang)|capitalize }}</option>
        {% endfor %}
    </select>

    <div class="translations-list">
        {% for lang in acceptedLanguages %}
            <div data-language-widget-target="translationRow" data-lang="{{ lang }}">
                <span>{{ lang|upper }}</span>
                <button data-action="click->language-widget#editTranslation">
                    <i class="fas fa-plus text-muted"></i>
                </button>
                <input data-language-widget-target="titleInput" readonly>
            </div>
        {% endfor %}
    </div>
</div>
```

## Communication entre contrôleurs

### Méthode 1 : Événements custom

```javascript
// Contrôleur émetteur
this.dispatch('languageChanged', { detail: { language: 'fr' } });

// Contrôleur récepteur (sur un parent ou le même élément)
<div data-action="language-widget:languageChanged->editor#handleLanguageChange">
```

### Méthode 2 : Outlets (références entre contrôleurs)

```javascript
// Contrôleur parent
export default class extends Controller {
  static outlets = ['editor'];

  updateEditor() {
    this.editorOutlet.refresh(); // Appelle une méthode de l'autre contrôleur
  }
}
```

```twig
<div data-controller="page" data-page-editor-outlet="#my-editor">
    <div id="my-editor" data-controller="editor">
    </div>
</div>
```

## Vanilla JS vs Stimulus : comparaison

### Vanilla JS
```javascript
document.addEventListener('DOMContentLoaded', () => {
  const select = document.querySelector('#language-select');
  const output = document.querySelector('#output');

  if (select) {
    select.addEventListener('change', (e) => {
      output.textContent = e.target.value;
    });
  }
});
```

### Stimulus
```javascript
// language_controller.js
export default class extends Controller {
  static targets = ['output'];

  switch(event) {
    this.outputTarget.textContent = event.target.value;
  }
}
```

```twig
<div data-controller="language">
    <select data-action="change->language#switch">
    <span data-language-target="output"></span>
</div>
```

## Bonnes pratiques

1. **Un contrôleur = une responsabilité**
   - Éviter les contrôleurs monolithiques
   - Préférer plusieurs petits contrôleurs

2. **Nommage explicite**
   - `search_controller.js` pas `s_controller.js`
   - `data-action="click->modal#close"` pas `data-action="click->modal#c"`

3. **Utiliser les values pour la configuration**
   - Pas de valeurs hardcodées dans le JS
   - Tout doit être configurable depuis le HTML

4. **Préférer les événements pour la communication**
   - Découplage entre contrôleurs
   - Plus facile à tester

## Ressources

- [Documentation officielle Stimulus](https://stimulus.hotwired.dev/)
- [Stimulus Handbook](https://stimulus.hotwired.dev/handbook/introduction)
- [Symfony UX Stimulus](https://symfony.com/bundles/StimulusBundle/current/index.html)
- [Awesome Stimulus](https://github.com/stimulus-components/stimulus-components)

## Turbo (optionnel)

Stimulus fonctionne très bien seul, mais peut être combiné avec **Turbo** pour une navigation SPA :

| Librairie | Rôle |
|-----------|------|
| Stimulus | Organisation du JavaScript |
| Turbo Drive | Navigation sans rechargement |
| Turbo Frames | Mise à jour partielle de la page |
| Turbo Streams | Mises à jour en temps réel |

Pour ce projet, **Stimulus seul suffit**. Turbo peut être ajouté plus tard si nécessaire.