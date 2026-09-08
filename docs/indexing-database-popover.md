# Hover Card Popover - Indexing Databases

## Overview

This feature adds a hover preview card on database names in the admin indexing databases list. When users hover over a database name, a popover displays key information (logo, status, URL, journal count) without leaving the page.

## Files

| File | Purpose |
|------|---------|
| `assets/controllers/indexing_database_popover_card_controller.js` | Stimulus controller |
| `assets/styles/app.scss` | Popover CSS styles |
| `templates/indexingDatabase/admin/index.html.twig` | Template with data attributes |
| `translations/messages.en.yaml` | English translations |
| `translations/messages.fr.yaml` | French translations |

## Stimulus Controller

### Location
`assets/controllers/indexing_database_popover_card_controller.js`

### Values (data attributes)

| Value | Type | Description |
|-------|------|-------------|
| `logo` | String | URL of the database logo |
| `status` | String | Status value (pending, validated, rejected) |
| `statusLabel` | String | Translated status label |
| `url` | String | Database URL |
| `journalCount` | Number | Number of associated journals |
| `clickText` | String | Text shown at bottom ("Click to see full details") |

### How it works

1. **connect()**: Creates a Bootstrap Popover on the element with `trigger: 'hover focus'`
2. **disconnect()**: Disposes the popover when element is removed
3. **buildContent()**: Builds HTML content with logo, status badge, URL, journal count
4. **statusColorClass**: Maps status to Bootstrap color (warning/success/danger)

## Template Usage

```twig
<a href="{{ path('app_admin_indexing_database_show', {id: db.id}) }}"
   data-controller="indexing-database-popover-card"
   data-indexing-database-popover-card-logo-value="{{ db.logo ? path('app_indexing_database_logo', {filename: db.logo}) : '' }}"
   data-indexing-database-popover-card-status-value="{{ db.status.value }}"
   data-indexing-database-popover-card-status-label-value="{{ ('indexingDatabase.status.' ~ db.status.value)|trans }}"
   data-indexing-database-popover-card-url-value="{{ db.url|default('') }}"
   data-indexing-database-popover-card-journal-count-value="{{ db.reviews|length }}"
   data-indexing-database-popover-card-click-text-value="{{ 'indexingDatabase.popover.clickForDetails'|trans }}">
    {{ db.name }}
</a>
```

## CSS Styles

Located in `assets/styles/app.scss`:

```scss
// Popover card styles
.popover-card {
  .popover-logo {
    max-height: 30px !important;
    max-width: 100px !important;
    width: auto !important;
    height: auto !important;
    object-fit: contain;
    display: block;
  }
}
```

The `customClass: 'popover-card'` option in the Stimulus controller applies this class to the popover element.

## Translations

### English (`messages.en.yaml`)
```yaml
indexingDatabase:
  popover:
    clickForDetails: "Click to see full details"
```

### French (`messages.fr.yaml`)
```yaml
indexingDatabase:
  popover:
    clickForDetails: "Cliquez pour voir tous les détails"
```

## Popover Content Structure

```html
<div style="min-width: 250px;">
  <img src="..." alt="" class="popover-logo mb-2">
  <div><strong>Status:</strong> <span class="badge bg-success">Validated</span></div>
  <div class="text-truncate" style="max-width: 230px;"><strong>URL:</strong> https://...</div>
  <div><strong>Journals:</strong> 3</div>
  <div class="text-muted mt-2"><small>Click to see full details</small></div>
</div>
```

## Behavior

- **Trigger**: Hover or focus on the database name link
- **Placement**: Right side of the element
- **Click action**: Navigates to the database detail page (`app_admin_indexing_database_show`)
- **Auto-dispose**: Popover is disposed when element disconnects from DOM

## Customization

### Change logo size
Edit `assets/styles/app.scss`:
```scss
.popover-card {
  .popover-logo {
    max-height: 40px !important;  // Increase height
    max-width: 120px !important;  // Increase width
  }
}
```

### Change popover placement
Edit the Stimulus controller `connect()` method:
```javascript
this.popover = new bootstrap.Popover(this.element, {
  placement: 'top',  // Options: top, bottom, left, right
  // ...
});
```

### Add more fields
1. Add new value in `static values`
2. Add corresponding `data-...-value` attribute in template
3. Add HTML in `buildContent()` method