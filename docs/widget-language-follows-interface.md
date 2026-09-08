# Page Edit: Content Follows Interface Language

## Overview

This document describes the synchronization between the interface language (`app.request.locale`) and the page content. When loading a page (view or edit mode), the content follows the interface language **without fallback** - if no content exists for the selected language, a "No content available" message is shown instead of falling back to the default language.

## Concepts

| Concept | Variable | Controls |
|---------|----------|----------|
| **Interface Language** | `app.request.locale` | URL, UI translations (buttons, labels, menus) |
| **Widget Language** | User selection in dropdown | Which language is being viewed/edited |
| **Default Language** | `defaultLanguage` (from journal settings) | Fallback for titles only (from YAML) |
| **currentLang** | JavaScript variable | Current language being viewed/edited (synced with widget) |

## Behavior

### On Page Load (View Mode)

1. **Widget Dropdown**: Synchronized with interface language (`currentLang`)
2. **Title**: Displays in interface language (with fallback to default language - titles come from YAML)
3. **Content**: Displays in interface language **without fallback** - shows "No content available" if empty

### On Page Load (Edit Mode)

1. **Widget Dropdown**: Synchronized with interface language (`currentLang`)
2. **Title**: Displays in interface language (with fallback to default language - read-only)
3. **Content**: Editor shows content for interface language **without fallback** - empty if no content

### When Changing Widget Language

- Title and content switch to the selected language
- **Content**: No fallback - shows empty (edit) or "No content available" (view) if missing
- **Title**: Falls back to default language (titles come from YAML configuration)

## Example

| Interface URL | Widget Dropdown | Content Displayed |
|---------------|-----------------|-------------------|
| `/fr/journal/xxx/pages/about` | `fr` (French) | French content or "Pas de contenu disponible" |
| `/en/journal/xxx/pages/about` | `en` (English) | English content or "No content available" |
| `/fr/journal/xxx/pages/about/edit` | `fr` (French) | French content in editor (empty if none) |

## Implementation

### 1. Page Config (Template)

**File:** `templates/pages/journalPages.html.twig`

Passes both HTML content (for display) and Markdown content (for saving):

```javascript
window.pageConfig = {
  locale: {{ app.request.locale|json_encode|raw }},
  // ...
  pageData: {
    title: {{ currentPageData.title|json_encode|raw }},
    content: {{ currentPageData.content|json_encode|raw }},           // HTML for CKEditor
    markdownContent: {{ currentPageData.markdownContent|json_encode|raw }}  // Markdown for saving
  }
};
```

### 2. View Mode Template

**File:** `templates/pages/journalPages.html.twig`

Uses `app.request.locale` for initial content display **without fallback**:

```twig
{# Use interface locale, NO fallback to default language #}
{% set localeContent = currentPageData.content[app.request.locale]|default('') %}
{% if localeContent is not empty %}
    {{ localeContent|raw }}
{% else %}
    <p class="text-muted fst-italic no-content-message">{{ 'journalPages.noContentAvailable'|trans }}</p>
{% endif %}
```

### 3. Edit Form Template

**File:** `templates/pages/_page_form.html.twig`

Uses `initialLang` for initial content **without fallback**:

```twig
{% set initialLang = app.request.locale in acceptedLanguages ? app.request.locale : defaultLanguage %}

<input type="hidden" name="language" id="form-language" value="{{ initialLang }}">

{# Title uses fallback (read-only, from YAML) #}
value="{{ pageData.title[initialLang]|default(pageData.title[defaultLanguage]|default('')) }}"

{# Content: NO fallback - empty if no content for this language #}
{{ pageData.markdownContent[initialLang]|default('') }}
```

### 4. JavaScript Initialization

**File:** `assets/scripts/pages/journalPages.js`

#### currentLang Initialization

```javascript
let currentLang = config.acceptedLanguages.includes(config.locale)
    ? config.locale
    : config.defaultLanguage;
```

#### Widget Synchronization

At page load, the widget dropdown is synchronized with `currentLang`:

```javascript
if (sidebarWidget?.select) {
  sidebarWidget.select.value = currentLang;
}
```

#### Edit Mode Initialization

In edit mode, title and CKEditor content are updated to match interface language:

```javascript
if (config.editMode && (titleInputOnLoad || contentInputOnLoad)) {
  const data = translations[currentLang] || { title: '', content: '' };
  const defaultData = translations[config.defaultLanguage] || { title: '', content: '' };

  // Update title immediately (with fallback - titles come from YAML)
  if (titleInputOnLoad) {
    titleInputOnLoad.value = data.title || defaultData.title || '';
  }

  // Initialize CKEditor and load content (NO fallback)
  (async () => {
    await initPageEditor();
    loadLanguage(currentLang);
  })();
}
```

#### View Mode Initialization

In view mode, content is updated via `updateViewContent()`:

```javascript
if (!config.editMode && config.currentPage) {
  updateViewContent(currentLang);
}
```

### 5. loadLanguage Function

Updates form content when language changes:

```javascript
function loadLanguage(lang) {
  currentLang = lang;
  const data = translations[lang] || { title: '', content: '' };
  const defaultData = translations[config.defaultLanguage] || { title: '', content: '' };

  // Update title with fallback (title is read-only, comes from YAML)
  if (titleInput) {
    titleInput.value = data.title || defaultData.title || '';
  }

  // Update CKEditor content - NO fallback, show empty if no content
  if (editorInitialized) {
    setEditorContent(data.content || '');
  }
}
```

### 6. updateViewContent Function

Updates view content when language changes (no fallback):

```javascript
function updateViewContent(lang) {
  // Title: keep fallback (titles come from YAML)
  const title =
    config.pageData.title?.[lang] ||
    config.pageData.title?.[config.defaultLanguage] ||
    '';

  // Content: NO fallback - show "no content" message if empty
  const content = config.pageData.content?.[lang] || '';

  if (content) {
    contentElement.innerHTML = content;
  } else {
    contentElement.innerHTML = `<p class="text-muted fst-italic">No content available</p>`;
  }
}
```

## Data Flow

```
Page Load
    |
    v
Twig generates HTML with app.request.locale (NO fallback for content)
    |
    v
JavaScript initializes:
  1. currentLang = config.locale (or defaultLanguage)
  2. initTranslations() loads content from config.pageData
  3. Widget dropdown synced with currentLang
  4. Title updated with translations[currentLang] (fallback OK)
  5. Content updated WITHOUT fallback
    |
    v
User changes widget language
    |
    v
handleLanguageChange(selectedLang):
  - View mode: updateViewContent(selectedLang) - NO fallback
  - Edit mode: saveCurrentLanguage() + loadLanguage(selectedLang) - NO fallback
```

## Content Types

| Type | Source | Usage |
|------|--------|-------|
| `content` (HTML) | `currentPageData.content` | Display in view mode / CKEditor |
| `markdownContent` | `currentPageData.markdownContent` | Stored in hidden inputs for saving |

## Fallback Logic Summary

| Element | View Mode | Edit Mode |
|---------|-----------|-----------|
| **Title** | Fallback to default language | Fallback to default language |
| **Content** | NO fallback - shows "No content available" | NO fallback - editor is empty |

### Why No Fallback for Content?

1. **Clarity**: Makes it clear that content needs to be added for this language
2. **Avoid confusion**: Users won't accidentally edit content thinking it's in their language
3. **Explicit translation**: Encourages proper translation rather than relying on fallbacks

## Files Modified

| File | Changes |
|------|---------|
| `templates/pages/journalPages.html.twig` | View mode uses `app.request.locale` without fallback |
| `templates/pages/_page_form.html.twig` | Edit form uses `initialLang` without fallback for content |
| `assets/scripts/pages/journalPages.js` | `loadLanguage()` and `updateViewContent()` without fallback |

## Related Documentation

- [Language Widget: Pages vs News](./language-widget-pages-vs-news.md)
- [Page Content Language Fallback](./page-content-language-fallback.md)