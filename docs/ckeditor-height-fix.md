# CKEditor Height Configuration Fix

## Problem

When editing page content with little text, users had to click the "Save" button twice:
1. First click: The editor would resize/shrink
2. Second click: Actually save the content

This was caused by dynamic height adjustment logic in CKEditor that conflicted with CSS styles.

## Root Cause

In `assets/scripts/components/ckeditor.js`, there was JavaScript code that dynamically adjusted the editor's minimum height based on `scrollHeight`:

```javascript
// REMOVED CODE
const updateMinHeight = () => {
  const h = Math.max(420, editableEl.scrollHeight || 0);
  editableEl.style.minHeight = h + 'px';
};
editor.editing.view.document.on('change', updateMinHeight);
```

This caused:
- The editor to grow but never shrink
- Height changes when focus changed
- The first click on "Save" triggered a layout recalculation instead of form submission

## Solution

### 1. Removed Dynamic Height Logic

Removed the `updateMinHeight` function from `ckeditor.js`. The editor now uses pure CSS for height control.

### 2. CSS Height Configuration

Height is now controlled entirely through SCSS files:

#### For Pages (`assets/styles/pages/journalPages.scss`)

```scss
// Edit mode - form container
.page-form-card {
  #page_content {
    min-height: 300px;  // Container for CKEditor

    @media (max-width: 768px) {
      min-height: 200px;
    }
  }
}

// View mode - rendered content display
#page-body,
.ck-content {
  min-height: 400px;
}

// CKEditor editable area (global)
.ck.ck-editor {
  .ck-editor__editable {
    min-height: 200px;
    max-height: 60vh;
    overflow-y: auto;
  }

  @media (max-width: 768px) {
    .ck-editor__editable {
      min-height: 150px;
      max-height: 40vh;
    }
  }
}
```

#### For News (`assets/styles/pages/news.scss`)

```scss
// CKEditor container
#news_content {
  min-height: 400px;

  @media (max-width: 768px) {
    min-height: 250px;
  }
}

// CKEditor editable area
.ck.ck-editor {
  .ck-editor__editable {
    min-height: 400px;
    max-height: 60vh;
    overflow-y: auto;
  }

  @media (max-width: 768px) {
    .ck-editor__editable {
      min-height: 200px;
      max-height: 50vh;
    }
  }
}
```

### 3. Removed Inline Styles

Removed inline `style="min-height: 200px;"` from template files to allow CSS control:

- `templates/pages/journalPages.html.twig` - `#page-view-content` element

## CSS Selectors Reference

| Selector | Purpose | File |
|----------|---------|------|
| `#page_content` | Edit form container (pages) | journalPages.scss |
| `#news_content` | Edit form container (news) | news.scss |
| `.ck-editor__editable` | CKEditor editable textarea | Both files |
| `.ck-content` | Rendered content view mode | journalPages.scss |
| `#page-view-content` | Page content view container | journalPages.scss |
| `#page-body` | Page body view container | journalPages.scss |

## Files Modified

1. `assets/scripts/components/ckeditor.js` - Removed dynamic height logic
2. `assets/styles/pages/journalPages.scss` - CSS height configuration for pages
3. `assets/styles/pages/news.scss` - CSS height configuration for news
4. `templates/pages/journalPages.html.twig` - Removed inline style

## Testing

After making changes, rebuild assets:

```bash
npm run build
```

Verify:
1. Edit a page with little content - Save button should work on first click
2. Edit a news item - Editor should have appropriate height
3. View mode should display content with correct minimum height