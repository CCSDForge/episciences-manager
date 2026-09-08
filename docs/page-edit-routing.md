# Page Edit Routing

## Overview

This document explains the routing logic for journal pages editing in the episciences-manager application.

## Routes

| Route Name | Path | Method | Description |
|------------|------|--------|-------------|
| `app_journal_pages` | `/journal/{code}/pages` | GET | List all pages |
| `app_journal_page_view` | `/journal/{code}/pages/{pageCode}` | GET | View a page (read-only) |
| `app_journal_page_edit` | `/journal/{code}/pages/{pageCode}/edit` | GET/POST | Edit a page |

## User Flow

```
┌─────────────────────────────────┐
│  /journal/epijinfo/pages/about  │
│  (View Mode)                    │
│                                 │
│  ┌───────────────────────────┐  │
│  │ Page Content (read-only)  │  │
│  │ - Title                   │  │
│  │ - Content                 │  │
│  └───────────────────────────┘  │
│                                 │
│  [Edit Button] ─────────────────┼──┐
│                                 │  │
│  Language Widget:               │  │
│  [EN ✏️] [FR +] ────────────────┼──┤
└─────────────────────────────────┘  │
                                     │
                                     ▼
┌─────────────────────────────────┐
│  /journal/epijinfo/pages/about/edit │
│  (Edit Mode)                    │
│                                 │
│  ┌───────────────────────────┐  │
│  │ Edit Form                 │  │
│  │ - Title (read-only)       │  │
│  │ - Content (CKEditor)      │  │
│  └───────────────────────────┘  │
│                                 │
│  [Cancel] ──────────────────────┼──► Back to View
│  [Save] ────────────────────────┼──► POST then redirect to View
└─────────────────────────────────┘
```

## Architecture Decision

### Previous Approach: Collapse-based editing

The edit form was embedded in the view page as a Bootstrap collapse component:
- Same URL for view and edit (`/pages/about`)
- Click "Edit" button → collapse expands
- Form submission → page reloads

**Issues:**
- URL doesn't reflect the current state (view vs edit)
- Browser back/forward buttons don't work as expected
- Cannot bookmark or share a direct link to edit mode
- More complex state management in JavaScript

### Current Approach: Separate pages

View and edit are distinct pages with separate URLs:
- View: `/pages/about`
- Edit: `/pages/about/edit`

**Benefits:**
- RESTful URL pattern (standard web convention)
- URL is meaningful and shareable
- Browser navigation works naturally
- Simpler code, easier to maintain
- Clear separation of concerns

## Implementation Details

### Controller (`PageController.php`)

```php
// View route - displays page content (read-only)
#[Route('/journal/{code}/pages/{pageCode}', name: 'app_journal_page_view')]
public function pageView(...): Response
{
    // Returns template with editMode: false
    return $this->render('pages/journalPages.html.twig', [
        // ...
        'editMode' => false,
    ]);
}

// Edit route - displays edit form
#[Route('/journal/{code}/pages/{pageCode}/edit', name: 'app_journal_page_edit', methods: ['GET', 'POST'])]
public function pageEdit(...): Response
{
    // GET: Returns template with editMode: true
    // POST: Saves changes and redirects to view
    return $this->render('pages/journalPages.html.twig', [
        // ...
        'editMode' => true,
    ]);
}
```

### Template (`journalPages.html.twig`)

The template conditionally renders based on `editMode`:

```twig
{% if editMode|default(false) %}
    {# Edit Mode: Show form directly (open) #}
    {% include 'pages/_page_form.html.twig' with {
        formOpen: true
    } %}
{% else %}
    {# View Mode: Show content with Edit link #}
    <div class="card">
        {# Page content display #}
        <div class="card-footer">
            <a href="{{ path('app_journal_page_edit', {...}) }}" class="btn btn-primary">
                Edit
            </a>
        </div>
    </div>
{% endif %}
```

### Form Partial (`_page_form.html.twig`)

Supports `formOpen` parameter to control initial state:

```twig
{% set isFormOpen = formOpen|default(false) %}

{# No 'collapse' class when formOpen is true #}
<div class="{% if not isFormOpen %}collapse{% endif %}" id="pageEditForm">
    {# Form content #}
</div>
```

### JavaScript (`journalPages.js`)

Handles language widget interactions:

```javascript
function handleTranslationClick(lang) {
    if (config.editMode || formIsOpen) {
        // Already in edit mode, just switch language
        loadLanguage(lang);
    } else if (config.currentPage) {
        // View mode: redirect to edit page
        window.location.href = `/${config.locale}/journal/${config.journalCode}/pages/${config.currentPage}/edit`;
    }
}
```

## Language Widget Behavior

| Mode | Click on language icon | Result |
|------|------------------------|--------|
| View | Click pencil (✏️) or plus (+) | Redirect to `/edit` |
| Edit | Click pencil (✏️) or plus (+) | Switch language in editor |

## Related Files

- `src/Controller/PageController.php` - Route definitions and logic
- `templates/pages/journalPages.html.twig` - Main template
- `templates/pages/_page_form.html.twig` - Edit form partial
- `assets/scripts/pages/journalPages.js` - JavaScript handling