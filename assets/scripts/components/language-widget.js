/**
 * Language Widget Component
 * Reusable component for language selection and translations management
 */

/**
 * Update the sidebar language select visibility:
 * show only languages that have existing content for the current page.
 * When no page is selected (contentByLocale is null), show all accepted languages.
 * @param {Object|null} contentByLocale - Object with locale keys and content values
 * @param {string} selectId - ID of the select element
 */
export function updateLanguageSelectOptions(contentByLocale, selectId = 'sidebar-language-select') {
  const select = document.getElementById(selectId);
  if (!select) return;

  Array.from(select.options).forEach(option => {
    if (!contentByLocale) {
      option.hidden = false;
    } else {
      const hasContent =
        contentByLocale[option.value] &&
        contentByLocale[option.value].trim() !== '';
      option.hidden = !hasContent;
    }
  });

  if (select.selectedOptions[0]?.hidden) {
    const firstVisible = Array.from(select.options).find(o => !o.hidden);
    if (firstVisible) firstVisible.selected = true;
  }
}

/**
 * Update the translations list in the sidebar widget.
 * - Pencil icon + title if content exists for that language
 * - "+" icon + empty field if no content
 * @param {Object} titleByLocale - Object with locale keys and title values
 * @param {Object} contentByLocale - Object with locale keys and content values
 * @param {string} listId - ID of the translations list container
 * @param {Object} translations - Translation strings for tooltips
 */
export function updateTranslationsList(
  titleByLocale,
  contentByLocale,
  listId = 'translations-list',
  translations = {}
) {
  const list = document.getElementById(listId);
  if (!list) return;

  list.querySelectorAll('.translation-row').forEach(row => {
    const lang = row.getAttribute('data-lang');
    const actionBtn = row.querySelector('.translation-action-btn');
    const titleInput = row.querySelector('.translation-title-input');
    const icon = actionBtn?.querySelector('i');

    const hasContent = contentByLocale?.[lang]?.trim();
    const title = titleByLocale?.[lang] || '';
    const hasTitle = title.trim() !== '';

    // Show pencil if either title OR content exists
    if (hasContent || hasTitle) {
      if (icon) icon.className = 'fas fa-pencil-alt text-primary';
      actionBtn?.setAttribute(
        'title',
        translations.editTranslation || 'Edit'
      );
      if (titleInput) titleInput.value = title;
    } else {
      if (icon) icon.className = 'fas fa-plus text-muted';
      actionBtn?.setAttribute(
        'title',
        translations.addTranslation || 'Add'
      );
      if (titleInput) titleInput.value = '';
    }
  });
}

/**
 * Initialize the language widget
 * @param {Object} [options] - Configuration options
 * @param {string} [options.widgetId='language'] - Widget ID prefix for generating element IDs
 * @param {string} [options.selectId] - Override ID of the language select element
 * @param {string} [options.listId] - Override ID of the translations list container
 * @param {string} [options.collapseTargetId] - Override ID of the collapsible body
 * @param {Function} [options.onLanguageChange] - Callback when language changes
 * @param {Function} [options.onTranslationClick] - Callback when translation button is clicked
 * @returns {Object|null} - Widget API or null if initialization failed
 */
export function initLanguageWidget(options = {}) {
  const {
    widgetId = 'language',
    selectId = null,
    listId = null,
    collapseTargetId = null,
    onLanguageChange = null,
    onTranslationClick = null,
  } = options;

  // Use widgetId to generate default IDs (matching Twig template)
  const finalSelectId = selectId || `${widgetId}-language-select`;
  const finalListId = listId || `${widgetId}-translations-list`;
  const finalCollapseId = collapseTargetId || `${widgetId}-widget-body`;

  const select = document.getElementById(finalSelectId);
  const list = document.getElementById(finalListId);
  const collapseBody = document.getElementById(finalCollapseId);

  if (!select) {
    console.warn(`Language widget: select element #${finalSelectId} not found`);
    return null;
  }

  // Language change event
  if (onLanguageChange) {
    select.addEventListener('change', e => {
      onLanguageChange(e.target.value);
    });
  }

  // Translation button click events
  if (onTranslationClick && list) {
    list.querySelectorAll('.translation-action-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const row = this.closest('.translation-row');
        const lang = row?.getAttribute('data-lang');
        if (lang) {
          onTranslationClick(lang);
        }
      });
    });
  }

  // Collapse toggle icon animation
  const collapseToggle = document.querySelector(
    `[data-bs-target="#${finalCollapseId}"]`
  );
  if (collapseToggle && collapseBody) {
    collapseBody.addEventListener('shown.bs.collapse', function () {
      const icon = collapseToggle.querySelector('i');
      if (icon) {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
      }
    });

    collapseBody.addEventListener('hidden.bs.collapse', function () {
      const icon = collapseToggle.querySelector('i');
      if (icon) {
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
      }
    });
  }

  // Return widget API
  return {
    select,
    list,
    getValue: () => select.value,
    setValue: lang => {
      select.value = lang;
    },
    updateOptions: contentByLocale =>
      updateLanguageSelectOptions(contentByLocale, finalSelectId),
    updateTranslations: (titleByLocale, contentByLocale, translations) =>
      updateTranslationsList(
        titleByLocale,
        contentByLocale,
        finalListId,
        translations
      ),
  };
}
