# Guide: Ajouter un nouveau champ dans Backoffice Settings

Ce document explique le processus complet pour ajouter un nouveau paramètre dans la page **Backoffice Settings** d'un journal.

## Architecture (Symfony + Stimulus)

Cette page utilise une approche **hybride** :
- **Symfony** : Gère le formulaire (validation, CSRF, persistance)
- **Stimulus** : Améliore l'UX (loading state)

### Routes RESTful

| Méthode | URL | Nom | Action |
|---------|-----|-----|--------|
| GET | `/journal/{code}/backoffice-settings` | `app_journal_backoffice_settings` | Affichage lecture seule |
| GET | `/journal/{code}/backoffice-settings/edit` | `app_journal_backoffice_settings_edit` | Formulaire éditable |
| POST | `/journal/{code}/backoffice-settings/edit` | `app_journal_backoffice_settings_update` | Sauvegarde |

### Flux utilisateur

```
┌─────────────────────────────────────┐
│  /backoffice-settings (lecture)     │
│                                     │
│  Description: Lorem ipsum...        │
│  Keywords: science, research...     │
│  Year: 2020                         │
│                                     │
│         [Modifier]                  │
└────────────┬────────────────────────┘
             │ click
             ▼
┌─────────────────────────────────────┐
│  /backoffice-settings/edit          │
│                                     │
│  Description: [textarea_______]     │
│  Keywords: [input_____________]     │
│  Year: [input___]                   │
│                                     │
│    [Annuler]  [Sauvegarder]         │
└────────────┬────────────────────────┘
             │ submit POST
             ▼
┌─────────────────────────────────────┐
│  /backoffice-settings (lecture)     │
│                                     │
│  ✅ Settings saved successfully     │
│                                     │
│  Description: Lorem ipsum...        │
│  ...                                │
└─────────────────────────────────────┘
```

### Diagramme d'architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         ARCHITECTURE HYBRIDE                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   Browser (User)                                                             │
│        │                                                                     │
│        ▼                                                                     │
│   ┌─────────────────────────────────────────┐                               │
│   │  Template (Twig)                        │                               │
│   │  index.html.twig                        │                               │
│   │  - Variable editMode (true/false)       │                               │
│   │  - Mode lecture: affiche valeurs        │                               │
│   │  - Mode édition: affiche formulaire     │                               │
│   │  - Stimulus pour UX (optionnel)         │                               │
│   └─────────────────┬───────────────────────┘                               │
│                     │                                                        │
│                     │  POST (form submit) vers /edit                         │
│                     ▼                                                        │
│   ┌─────────────────────────────────────────┐                               │
│   │  Controller (PHP)                       │                               │
│   │  JournalBackofficeSettingController.php │                               │
│   │  - index(): GET lecture seule           │                               │
│   │  - edit(): GET formulaire               │                               │
│   │  - update(): POST sauvegarde            │                               │
│   └─────────────────┬───────────────────────┘                               │
│                     │                                                        │
│                     ▼                                                        │
│   ┌─────────────────────────────────────────┐                               │
│   │  Service                                │                               │
│   │  JournalBackofficeSettingService.php    │                               │
│   │  - Logique métier                       │                               │
│   │  - Lecture/écriture des settings        │                               │
│   └─────────────────┬───────────────────────┘                               │
│                     │                                                        │
│                     ▼                                                        │
│   ┌─────────────────────────────────────────┐                               │
│   │  Repository                             │                               │
│   │  JournalBackofficeSettingRepository.php │                               │
│   │  - Requêtes Doctrine                    │                               │
│   └─────────────────┬───────────────────────┘                               │
│                     │                                                        │
│                     ▼                                                        │
│   ┌─────────────────────────────────────────┐                               │
│   │  Entity                                 │                               │
│   │  JournalBackofficeSetting.php           │                               │
│   │  - Mapping ORM vers REVIEW_SETTING      │                               │
│   └─────────────────┬───────────────────────┘                               │
│                     │                                                        │
│                     ▼                                                        │
│   ┌─────────────────────────────────────────┐                               │
│   │  Base de données                        │                               │
│   │  Table: REVIEW_SETTING                  │                               │
│   │  Colonnes: RVID, SETTING, VALUE         │                               │
│   └─────────────────────────────────────────┘                               │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Avantages de l'approche

| Aspect | Avantage |
|--------|----------|
| URLs explicites | `/edit` visible quand on modifie |
| Séparation lecture/écriture | Plus RESTful |
| Protection accidentelle | Pas de modification par erreur |
| Symfony natif | Flash messages, CSRF, PRG pattern |
| Stimulus optionnel | Fonctionne sans JavaScript |

## Structure de la table REVIEW_SETTING

| Colonne | Type | Description |
|---------|------|-------------|
| `RVID` | INT | ID du journal (clé primaire composite) |
| `SETTING` | VARCHAR(200) | Nom du paramètre (clé primaire composite) |
| `VALUE` | TEXT | Valeur du paramètre |

## Fichiers impliqués

| Fichier | Rôle |
|---------|------|
| `src/Entity/JournalBackofficeSetting.php` | Entity Doctrine |
| `src/Repository/JournalBackofficeSettingRepository.php` | Requêtes DB |
| `src/Service/JournalBackofficeSettingService.php` | Logique métier |
| `src/Controller/JournalBackofficeSettingController.php` | 3 routes (index, edit, update) |
| `templates/journalBackofficeSettings/index.html.twig` | Template unique avec variable `editMode` |
| `assets/controllers/backoffice_settings_controller.js` | UX enhancement (loading) |
| `translations/messages.en.yaml` | Traductions EN |
| `translations/messages.fr.yaml` | Traductions FR |
| `src/mysql/*.sql` | Migration SQL |

---

## Étapes pour ajouter un nouveau champ

### Exemple: Ajouter `journalCreationYear`

### 1. Migration SQL

Créer ou modifier un fichier SQL pour initialiser le setting pour tous les journaux existants.

**Fichier:** `src/mysql/20260608_journal_description_table_review_settings.sql`

```sql
INSERT INTO REVIEW_SETTING (RVID, SETTING, VALUE)
SELECT RVID, 'journalCreationYear', NULL
FROM REVIEW
WHERE RVID NOT IN (
    SELECT RVID FROM REVIEW_SETTING WHERE SETTING = 'journalCreationYear'
);
```

### 2. Controller (PHP)

**Fichier:** `src/Controller/JournalBackofficeSettingController.php`

Ajouter le nouveau champ dans le tableau `$data` de la méthode `update()` :

```php
/**
 * Save settings and redirect to read-only view.
 */
#[Route('/journal/{code}/backoffice-settings/edit', name: 'app_journal_backoffice_settings_update', methods: ['POST'])]
public function update(
    string $code,
    Request $request,
    ReviewManager $reviewManager,
    JournalBackofficeSettingService $settingService
): Response {
    // ... vérifications ...

    $data = [
        'journalDescription' => $request->request->get('journalDescription'),
        'journalKeywords' => $request->request->get('journalKeywords'),
        'journalCreationYear' => $request->request->get('journalCreationYear'),  // ← Nouveau
    ];

    $settingService->updateSettings($review['rvid'], $data);

    $this->addFlash('success', 'journalBackofficeSettings.saved');

    return $this->redirectToRoute('app_journal_backoffice_settings', ['code' => $code]);
}
```

### 3. Template Twig

**Fichier:** `templates/journalBackofficeSettings/index.html.twig`

#### Structure du template (mode lecture/édition)

Le template utilise une variable `editMode` pour basculer entre les deux modes :

```twig
{# Mode édition: formulaire #}
{% if editMode %}
<form method="POST"
      action="{{ path('app_journal_backoffice_settings_update', {'code': code}) }}"
      data-controller="backoffice-settings"
      data-action="submit->backoffice-settings#onSubmit">
{% endif %}

{# ... cards ... #}

{# Boutons selon le mode #}
{% if editMode %}
    <input type="hidden" name="_token" value="{{ csrf_token('journal-backoffice-settings') }}">
    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ path('app_journal_backoffice_settings', {'code': code}) }}" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-times me-2"></i>{{ 'journalBackofficeSettings.cancel'|trans }}
        </a>
        <button type="submit" class="btn btn-primary btn-lg" data-backoffice-settings-target="saveButton">
            <i class="fas fa-save me-2"></i>{{ 'journalBackofficeSettings.save'|trans }}
        </button>
    </div>
    </form>
{% elseif canEditSettings %}
    <div class="d-flex justify-content-center mt-4">
        <a href="{{ path('app_journal_backoffice_settings_edit', {'code': code}) }}" class="btn btn-primary btn-lg">
            <i class="fas fa-edit me-2"></i>{{ 'journalBackofficeSettings.edit'|trans }}
        </a>
    </div>
{% else %}
    <div class="alert alert-info mt-4 text-center">
        {{ 'journalBackofficeSettings.readonlyInfo'|trans }}
    </div>
{% endif %}
```

#### Ajouter une nouvelle card

```twig
{# ===== Journal Creation Year ===== #}
<div class="card">
    <div class="card-header" style="font-size: 18px;">
        <i class="fas fa-calendar-alt me-2"></i>
        {{ 'journalBackofficeSettings.journalCreationYear.title'|trans }}
        <small class="text-muted">({{ 'journalBackofficeSettings.journalCreationYear.description'|trans }})</small>
    </div>
    <div class="card-body px-5">
        <div class="mb-3">
            <label for="journalCreationYear" class="form-label">
                {{ 'journalBackofficeSettings.journalCreationYear.label'|trans }}
            </label>
            {% if editMode %}
                <input type="number"
                       class="form-control"
                       id="journalCreationYear"
                       name="journalCreationYear"
                       value="{{ settings.journalCreationYear ?? '' }}"
                       placeholder="{{ 'journalBackofficeSettings.journalCreationYear.placeholder'|trans }}"
                       min="1950"
                       max="2100">
            {% else %}
                <div class="form-control bg-light">{{ settings.journalCreationYear ?? '-' }}</div>
            {% endif %}
            <div class="form-text">
                {{ 'journalBackofficeSettings.journalCreationYear.hint'|trans }}
            </div>
        </div>
    </div>
</div>
```

#### Points importants du template

| Variable | Valeur | Usage |
|----------|--------|-------|
| `editMode` | `true` / `false` | Bascule entre lecture et édition |
| `canEditSettings` | `true` / `false` | Affiche bouton "Modifier" si autorisé |
| `settings.nomDuChamp` | Valeur DB | Affichage de la valeur actuelle |

### 4. Stimulus Controller (optionnel, UX)

**Fichier:** `assets/controllers/backoffice_settings_controller.js`

Le controller Stimulus est simplifié - il ne gère que l'UX :

```javascript
import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for backoffice settings form.
 * Provides UX enhancements (loading state) while Symfony handles form submission.
 */
export default class extends Controller {
  static targets = ['saveButton'];

  /**
   * Handle form submission - add loading state to button
   * The form submits normally (Symfony handles POST)
   */
  onSubmit() {
    if (!this.hasSaveButtonTarget) return;

    // Show loading state
    this.saveButtonTarget.disabled = true;
    this.saveButtonTarget.innerHTML =
      '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
  }
}
```

> **Note:** Stimulus n'empêche pas la soumission du formulaire. Il ajoute juste un état visuel de chargement.

### 5. Traductions

**Fichier:** `translations/messages.en.yaml`

```yaml
journalBackofficeSettings:
  title: "Backoffice Settings"
  edit: "Edit"
  save: "Save"
  cancel: "Cancel"
  saved: "Settings saved successfully"
  error: "Error saving settings"
  readonlyInfo: "You can view these settings, but only epiadmin or administrator can modify them."

  journalCreationYear:
    title: "Creation Year"
    description: "Year the journal was founded"
    label: "Year"
    placeholder: "2020"
    hint: "Enter the year the journal was created."
```

**Fichier:** `translations/messages.fr.yaml`

```yaml
journalBackofficeSettings:
  title: "Paramètres back-office"
  edit: "Modifier"
  save: "Sauvegarder"
  cancel: "Annuler"
  saved: "Paramètres sauvegardés avec succès"
  error: "Erreur lors de la sauvegarde"
  readonlyInfo: "Vous pouvez consulter ces paramètres, mais seul epiadmin peut les modifier."

  journalCreationYear:
    title: "Année de création"
    description: "Année de fondation du journal"
    label: "Année"
    placeholder: "2020"
    hint: "Saisissez l'année de création du journal."
```

---

## Checklist pour un nouveau champ

- [ ] **SQL:** Ajouter INSERT pour initialiser le setting
- [ ] **Controller:** Ajouter le champ dans `$data` de `update()`
- [ ] **Twig:** Ajouter la card avec `{% if editMode %}` pour input/affichage
- [ ] **EN:** Ajouter traductions dans `messages.en.yaml`
- [ ] **FR:** Ajouter traductions dans `messages.fr.yaml`
- [ ] **Test:** Vérifier lecture et écriture fonctionnent

---

## Types d'inputs courants

| Type de donnée | Mode édition | Mode lecture |
|----------------|--------------|--------------|
| Texte court | `<input type="text" name="...">` | `<div class="form-control bg-light">{{ value }}</div>` |
| Texte long | `<textarea name="...">` | `<div class="form-control bg-light" style="white-space: pre-wrap;">` |
| Année | `<input type="number" min="1950" max="2100">` | `<div class="form-control bg-light">` |
| URL | `<input type="url">` | `<a href="{{ value }}">{{ value }}</a>` |
| Email | `<input type="email">` | `<a href="mailto:{{ value }}">{{ value }}</a>` |
| Choix unique | `<select name="...">` | `<div class="form-control bg-light">` |
| Oui/Non | `<input type="checkbox">` | `<i class="fas fa-check/fa-times">` |

---

## Conventions de nommage

| Contexte | Convention | Exemple |
|----------|------------|---------|
| Clé DB (SETTING) | camelCase avec préfixe `journal` | `journalCreationYear` |
| Attribut `name` HTML | Identique à la clé DB | `name="journalCreationYear"` |
| ID HTML | Identique à la clé DB | `id="journalCreationYear"` |
| Clé traduction | Hiérarchique avec dots | `journalBackofficeSettings.journalCreationYear.title` |
| Route lecture | `app_journal_backoffice_settings` | `/backoffice-settings` |
| Route édition | `app_journal_backoffice_settings_edit` | `/backoffice-settings/edit` |
| Route sauvegarde | `app_journal_backoffice_settings_update` | `/backoffice-settings/edit` (POST) |

---

## Flash Messages

Les messages flash sont gérés par Symfony et affichés dans le template :

```twig
{% for type, messages in app.flashes %}
    {% for message in messages %}
        <div class="alert alert-{{ type == 'error' ? 'danger' : type }} alert-dismissible fade show">
            {{ message|trans }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    {% endfor %}
{% endfor %}
```

Types de flash messages utilisés :
- `success` : Sauvegarde réussie
- `error` : Erreur (CSRF invalide, etc.)

---

## Sécurité

### CSRF Protection

Le token CSRF est inclus dans le formulaire (mode édition uniquement) :

```twig
{% if editMode %}
    <input type="hidden" name="_token" value="{{ csrf_token('journal-backoffice-settings') }}">
{% endif %}
```

Et validé côté serveur dans `update()` :

```php
if (!$this->isCsrfTokenValid('journal-backoffice-settings', $token)) {
    $this->addFlash('error', 'Invalid CSRF token');
    return $this->redirectToRoute('app_journal_backoffice_settings_edit', ['code' => $code]);
}
```

### Permissions

Trois niveaux de permission :

| Permission | Route | Accès |
|------------|-------|-------|
| `REVIEW_VIEW` | `index()` | Peut voir la page (lecture seule) |
| `REVIEW_EDIT_SETTINGS` | `edit()` | Peut accéder au formulaire |
| `REVIEW_EDIT_SETTINGS` | `update()` | Peut sauvegarder |

```php
// index() - lecture seule
$this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

// edit() et update() - modification
$this->denyAccessUnlessGranted('REVIEW_EDIT_SETTINGS', $review);
```

---

## Pattern PRG (Post-Redirect-Get)

Après la sauvegarde, le serveur redirige vers la page de lecture :

```
┌─────────┐     POST /edit    ┌─────────┐   302 Redirect   ┌─────────┐
│ Browser │ ────────────────► │ Server  │ ───────────────► │ Browser │
│  Form   │                   │ update()│                  │  GET /  │
└─────────┘                   └─────────┘                  └─────────┘
                                                                │
                                                                ▼
                                                           Page lecture
                                                           + flash message
```

Ce pattern évite la re-soumission du formulaire lors d'un refresh (F5).