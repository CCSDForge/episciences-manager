# Journal Settings Page Logic

This document explains how the journal settings page works, from schema validation to UI rendering.

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Data Flow                                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  config/schemas/journal_frontend_setting.json  ◄── Single source of truth    │
│                    │                                                 │
│                    ▼                                                 │
│  JournalFrontendSettingService.php ◄── Business logic, defaults     │
│                    │                                                 │
│                    ▼                                                 │
│  JournalFrontendSettingController.php ◄── Routes, permissions, JSON API │
│                    │                                                 │
│                    ▼                                                 │
│  index.html.twig ◄── UI rendering with Twig                         │
│                    │                                                 │
│                    ▼                                                 │
│  journalFrontendSettings.js ◄── Form collection, live preview, AJAX │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

## Key Files

| File | Purpose |
|------|---------|
| `config/schemas/journal_frontend_setting.json` | JSON Schema for validation |
| `src/Service/JournalFrontendSettingService.php` | Business logic, defaults, CRUD |
| `src/Entity/JournalFrontendSetting.php` | Database entity |
| `src/Controller/JournalFrontendSettingController.php` | HTTP routes |
| `templates/journalFrontendSettings/index.html.twig` | UI template |
| `assets/scripts/pages/journalFrontendSettings.js` | Frontend logic |
| `translations/messages.*.yaml` | UI labels |

---

## 1. JSON Schema (`config/schemas/journal_frontend_setting.json`)

The schema validates the entire settings structure. Main sections:

### Top-Level Properties

| Property | Type | Description |
|----------|------|-------------|
| `api_domain` | string | API endpoint URL |
| `theme` | object | Colors (`primaryColor`, `primaryTextColor`) |
| `languages` | object | `default` and `accepted` languages |
| `homepage` | object | 8 boolean render options |
| `homepageRightBlock` | object | `lastInformationRenderType` enum |
| `menu` | object | 12 boolean render options |
| `statistics` | object | Colors array + 7 stat options |

### Menu Options (boolean)

```json
{
  "menu": {
    "acceptedArticlesRender": { "type": "boolean" },
    "volumeTypeProceedingsRender": { "type": "boolean" },
    "specialIssuesRender": { "type": "boolean" },
    "sectionsRender": { "type": "boolean" },
    "authorsRender": { "type": "boolean" },
    "journalIndexingRender": { "type": "boolean" },
    "journalAcknowledgementsRender": { "type": "boolean" },
    "journalEthicalCharterRender": { "type": "boolean" },
    "journalForReviewersRender": { "type": "boolean" },
    "journalForEditorsRender": { "type": "boolean" },
    "journalForConferenceOrganisersRender": { "type": "boolean" },
    "journalProposingSpecialIssuesRender": { "type": "boolean" }
  }
}
```

### Adding a New Menu Option

To add a new menu setting (e.g., `journalForEditorsRender`):

1. **Schema** - Add to `config/schemas/journal_frontend_setting.json`:
   ```json
   "journalForEditorsRender": { "type": "boolean" }
   ```

2. **Service** - Add default value in `JournalFrontendSettingService.php`:
   ```php
   'journalForEditorsRender' => false,
   ```

3. **Template** - Add to appropriate category in `index.html.twig`:
   ```twig
   'publish': ['journalEthicalCharterRender', 'journalForReviewersRender', 'journalForEditorsRender', ...]
   ```

4. **Translations** - Add labels in `messages.*.yaml`:
   ```yaml
   journalFrontendSettings:
     menu:
       journalForEditorsRender: "For Editors"
   ```

---

## 2. Service (`JournalFrontendSettingService.php`)

### Responsibilities

- **Default values**: `getDefaultSetting()` returns complete default structure
- **Option lists**: `getHomepageOptions()`, `getMenuOptions()`, `getStatisticsOptions()`
- **CRUD operations**: `getOrCreateSetting()`, `getSettingArray()`, `updateSetting()`
- **Validation**: Delegates to `JsonSchemaValidator`

### Default Values Structure

```php
public function getDefaultSetting(): array
{
    return [
        'api_domain' => '...',
        'theme' => [
            'primaryColor' => '#563d7c',
            'primaryTextColor' => '#ffffff',
        ],
        'languages' => [...],
        'homepage' => [...],
        'homepageRightBlock' => [...],
        'menu' => [
            'acceptedArticlesRender' => true,
            'volumeTypeProceedingsRender' => true,
            // ...
            'journalForEditorsRender' => false,  // new option
            // ...
        ],
        'statistics' => [...],
    ];
}
```

### Merging Strategy

Uses `array_replace_recursive()` to merge saved settings with defaults:
- Missing keys get default values
- `languages.accepted` is replaced entirely (not merged)

---

## 3. Controller (`JournalFrontendSettingController.php`)

### Routes

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/journal/{code}/settings` | `index` | Render settings form |
| GET | `/journal/{code}/settings/show` | `show` | Return JSON settings |
| POST | `/journal/{code}/settings/edit` | `update` | Save settings |

### Index Action

```php
#[Route('/journal/{code}/frontend-settings', name: 'app_journal_frontend_settings')]
public function index(string $code): Response
{
    // Load journal, check permissions
    // Get merged settings from service
    // Pass to template: setting, homepageOptions, menuOptions, statsOptions, canEditSettings
}
```

### Update Action

```php
#[Route('/journal/{code}/frontend-settings/edit', name: 'app_journal_frontend_settings_edit', methods: ['POST'])]
public function update(string $code, Request $request): JsonResponse
{
    // Validate CSRF token
    // Parse JSON body
    // Call service->updateSetting()
    // Return success/error JSON
}
```

---

## 4. Template (`index.html.twig`)

### Menu Categories

The template defines three hardcoded categories:

```twig
{% set menuCategories = {
    'articlesAndIssues': ['acceptedArticlesRender', 'volumeTypeProceedingsRender', 'authorsRender', 'sectionsRender', 'specialIssuesRender'],
    'about': ['journalIndexingRender', 'journalAcknowledgementsRender', 'journalForConferenceOrganisersRender'],
    'publish': ['journalEthicalCharterRender', 'journalForReviewersRender', 'journalForEditorsRender', 'journalProposingSpecialIssuesRender']
} %}
```

### Rendering Menu Options

```twig
{% for category, options in menuCategories %}
    <div class="col-md-4">
        <h6>{{ ('journalFrontendSettings.menu.category.' ~ category)|trans }}</h6>
        {% for option in options %}
            <div class="form-check form-switch">
                <input class="form-check-input menu-option"
                       type="checkbox"
                       id="{{ option }}"
                       data-option="{{ option }}"
                       {{ setting.menu[option] ? 'checked' : '' }}>
                <label for="{{ option }}">
                    {{ ('journalFrontendSettings.menu.' ~ option)|trans }}
                </label>
            </div>
        {% endfor %}
    </div>
{% endfor %}
```

### Translation Keys

For menu options:
- Label: `journalFrontendSettings.menu.{optionName}` (e.g., `journalFrontendSettings.menu.journalForEditorsRender`)
- Category: `journalFrontendSettings.menu.category.{categoryName}` (e.g., `journalFrontendSettings.menu.category.publish`)

---

## 5. JavaScript (`journalFrontendSettings.js`)

### Form Data Collection

```javascript
function collectMenuData() {
    const menuData = {};
    document.querySelectorAll('.menu-option').forEach(checkbox => {
        menuData[checkbox.dataset.option] = checkbox.checked;
    });
    return menuData;
}
```

### Save Flow

1. Collect all form data into nested object
2. POST to `/journal/{code}/settings/edit?_token={csrf}`
3. Handle response (success alert or validation errors)

---

## 6. Menu Categories Explained

| Category | Options | Purpose |
|----------|---------|---------|
| **articlesAndIssues** | acceptedArticlesRender, volumeTypeProceedingsRender, authorsRender, sectionsRender, specialIssuesRender | Article/issue navigation |
| **about** | journalIndexingRender, journalAcknowledgementsRender, journalForConferenceOrganisersRender | "About" section links |
| **publish** | journalEthicalCharterRender, journalForReviewersRender, journalForEditorsRender, journalProposingSpecialIssuesRender | Submission guidance |

---

## 7. Complete Request Flow

```
User visits /journal/{code}/settings
    │
    ▼
Controller::index()
    ├── Load review by code
    ├── Check REVIEW_VIEW permission
    ├── service->getSettingArray() ── merges DB + defaults
    └── Render template with settings
    │
    ▼
Template renders form
    ├── Populates inputs from {{ setting }}
    ├── Groups menu options by category
    └── Injects CSRF token + update URL
    │
    ▼
User clicks Save
    │
    ▼
JavaScript collects form data
    ├── Uses .menu-option, .homepage-option selectors
    └── Builds nested object matching schema
    │
    ▼
POST /journal/{code}/settings/edit
    │
    ▼
Controller::update()
    ├── Validate CSRF
    ├── service->updateSetting()
    │       ├── JsonSchemaValidator validates
    │       ├── If valid: persist to DB
    │       └── If invalid: return errors
    └── Return JSON response
    │
    ▼
JavaScript handles response
    ├── Success: green alert
    └── Error: red alert with details
```

---

## 8. Database Entity

```php
// src/Entity/JournalFrontendSetting.php

#[ORM\Table(name: 'JOURNAL_SETTING')]
class JournalFrontendSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private int $rvid;           // Journal ID

    #[ORM\Column(type: 'json')]
    private array $setting = []; // Entire settings object

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;
}
```

---

## 9. Checklist: Adding a New Setting

### For a new menu option:

- [ ] `config/schemas/journal_frontend_setting.json` - Add property definition
- [ ] `src/Service/JournalFrontendSettingService.php` - Add default value in `getDefaultSetting()`
- [ ] `templates/journalFrontendSettings/index.html.twig` - Add to appropriate category in `menuCategories`
- [ ] `translations/messages.en.yaml` - Add English label
- [ ] `translations/messages.fr.yaml` - Add French label

### For a new homepage option:

- [ ] Schema: Add to `homepage` properties
- [ ] Service: Add to `getHomepageOptions()` and `getDefaultSetting()`
- [ ] Template: Will auto-render via `homepageOptions` loop
- [ ] Translations: Add labels

### For a new statistics option:

- [ ] Schema: Add to `statistics` properties with `$ref: "#/$defs/statOption"`
- [ ] Service: Add to `getStatisticsOptions()` and `getDefaultSetting()`
- [ ] Template: Will auto-render via `statsOptions` loop
- [ ] Translations: Add labels