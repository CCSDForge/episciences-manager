import {
  initializeCKEditor,
  getEditorContent,
  setEditorContent,
  destroyEditor,
  focusEditor,
} from '../components/ckeditor.js';


// Function to update inline edit translations dynamically
function updateInlineEditTranslations() {
  console.log(
    '=== updateInlineEditTranslations called ===',
    'translations available:',
    !!window.translations,
    'keys:',
    window.translations ? Object.keys(window.translations) : 'none'
  );

  if (!window.translations) {
    console.warn('No translations available');
    return;
  }

  // Update edit button
  const editButton = document.getElementById('edit-button');
  console.log('Edit button found:', !!editButton);
  if (editButton && window.translations.edit) {
    console.log('Updating edit button:', window.translations.edit);
    editButton.innerHTML =
      '<i class="fas fa-edit me-1"></i>' + window.translations.edit;
  }

  // Update inline edit title
  const inlineEditTitle = document.querySelector('#inline-edit-content h5');
  console.log('Inline edit title found:', !!inlineEditTitle);
  if (inlineEditTitle && window.translations.editPageContent) {
    console.log(
      'Updating inline edit title:',
      window.translations.editPageContent
    );
    const oldContent = inlineEditTitle.innerHTML;
    inlineEditTitle.innerHTML =
      '<i class="fas fa-edit me-2"></i>' +
      window.translations.editPageContent +
      '<i class="fas fa-file-alt ms-2"></i>';
    console.log(
      'Title updated from:',
      oldContent,
      'to:',
      inlineEditTitle.innerHTML
    );
  }

  // Update page title label (inline edit)
  const pageTitleLabel = document.querySelector(
    'label[for="page-title-inline"]'
  );
  console.log('Page title label found:', !!pageTitleLabel);
  if (pageTitleLabel && window.translations.pageTitle) {
    console.log(
      'Updating page title label from:',
      pageTitleLabel.textContent,
      'to:',
      window.translations.pageTitle
    );
    pageTitleLabel.textContent = window.translations.pageTitle;
  }

  // Update content label (inline edit)
  const contentLabel = document.querySelector(
    'label[for="page-content-inline"]'
  );
  console.log('Content label found:', !!contentLabel);
  if (contentLabel && window.translations.content) {
    console.log(
      'Updating content label from:',
      contentLabel.textContent,
      'to:',
      window.translations.content
    );
    contentLabel.textContent = window.translations.content;
  }

  // Update textarea placeholder (inline edit)
  const textarea = document.getElementById('page-content-inline');
  console.log('Textarea found:', !!textarea);
  if (textarea && window.translations.enterContent) {
    console.log(
      'Updating textarea placeholder from:',
      textarea.placeholder,
      'to:',
      window.translations.enterContent
    );
    textarea.placeholder = window.translations.enterContent;
  }

  // Update cancel button (inline edit)
  const cancelButton = document.getElementById('cancel-inline-edit');
  console.log('Cancel button found:', !!cancelButton);
  if (cancelButton && window.translations.cancel) {
    console.log('Updating cancel button:', window.translations.cancel);
    cancelButton.innerHTML =
      '<i class="fas fa-times me-1"></i>' + window.translations.cancel;
  }

  // Update save button (inline edit)
  const saveButton = document.getElementById('save-inline-edit');
  console.log('Save button found:', !!saveButton);
  if (saveButton && window.translations.save) {
    console.log('Updating save button:', window.translations.save);
    saveButton.innerHTML =
      '<i class="fas fa-save me-1"></i>' + window.translations.save;
  }

  // Update preview page button
  const previewPageButton = document.getElementById('preview-page-button');
  console.log('Preview page button found:', !!previewPageButton);
  console.log(
    'window.translations.previewPage:',
    window.translations.previewPage
  );
  console.log('All translation keys:', Object.keys(window.translations));
  if (previewPageButton && window.translations.previewPage) {
    console.log(
      'Updating preview page button:',
      window.translations.previewPage
    );
    previewPageButton.innerHTML =
      '<i class="fas fa-external-link-alt me-1"></i>' +
      window.translations.previewPage;
  } else {
    console.log(
      'Preview page button NOT updated. Button exists:',
      !!previewPageButton,
      'Translation exists:',
      !!window.translations.previewPage
    );
  }

  console.log('=== updateInlineEditTranslations completed ===');
}

// Function to update container titles when language changes
function updateContainerTitles(locale) {
  console.log('=== updateContainerTitles called ===', 'locale:', locale);

  // Update all container navigation links
  const containerLinks = document.querySelectorAll(
    '.page-nav-link[data-is-container="true"]'
  );
  console.log('Found container links:', containerLinks.length);

  containerLinks.forEach(link => {
    const titleEn = link.getAttribute('data-title-en');
    const titleFr = link.getAttribute('data-title-fr');

    if (titleEn && titleFr) {
      const newTitle = locale === 'fr' ? titleFr : titleEn;
      console.log(
        'Updating container title from:',
        link.textContent.trim(),
        'to:',
        newTitle
      );
      link.textContent = newTitle;
    }
  });

  // Update all sub-page navigation links
  updateSubPageLinks(locale);

  // Update Home links in navigation and breadcrumb
  updateHomeLinks(locale);

  // Update breadcrumb if it's currently visible
  updateBreadcrumbLanguage(locale);
}

// Function to update sub-page links
function updateSubPageLinks(locale) {
  console.log('=== updateSubPageLinks called ===', 'locale:', locale);

  // Find all sub-page links (those with data-current-title-en but not data-is-container)
  const subPageLinks = document.querySelectorAll(
    '.page-nav-link[data-current-title-en]:not([data-is-container])'
  );
  console.log('Found sub-page links:', subPageLinks.length);

  subPageLinks.forEach(link => {
    const currentTitleEn = link.getAttribute('data-current-title-en');
    const currentTitleFr = link.getAttribute('data-current-title-fr');

    if (currentTitleEn && currentTitleFr) {
      const newTitle = locale === 'fr' ? currentTitleFr : currentTitleEn;
      console.log(
        'Updating sub-page title from:',
        link.textContent.trim(),
        'to:',
        newTitle
      );
      link.textContent = newTitle;
    }
  });
}

// Function to update Home links
function updateHomeLinks(locale) {
  console.log('=== updateHomeLinks called ===', 'locale:', locale);

  // Update home navigation link
  const homeNavLink = document.querySelector('.home-nav-link');
  if (homeNavLink) {
    const homeTextEn = homeNavLink.getAttribute('data-home-text-en');
    const homeTextFr = homeNavLink.getAttribute('data-home-text-fr');

    if (homeTextEn && homeTextFr) {
      const newHomeText = locale === 'fr' ? homeTextFr : homeTextEn;
      console.log(
        'Updating home nav link from:',
        homeNavLink.textContent.trim(),
        'to:',
        newHomeText
      );
      homeNavLink.textContent = newHomeText;
    }
  }

  // Update breadcrumb home link
  const breadcrumbHome = document.querySelector('.breadcrumb-home');
  if (breadcrumbHome) {
    const homeTextEn = breadcrumbHome.getAttribute('data-home-text-en');
    const homeTextFr = breadcrumbHome.getAttribute('data-home-text-fr');

    if (homeTextEn && homeTextFr) {
      const newHomeText = locale === 'fr' ? homeTextFr : homeTextEn;
      const icon = breadcrumbHome.querySelector('i.fas.fa-home');
      console.log(
        'Updating breadcrumb home link from:',
        breadcrumbHome.textContent.trim(),
        'to:',
        newHomeText
      );

      if (icon) {
        breadcrumbHome.innerHTML = icon.outerHTML + ' ' + newHomeText;
      } else {
        breadcrumbHome.innerHTML =
          '<i class="fas fa-home me-1"></i> ' + newHomeText;
      }
    }
  }
}

// Function to update breadcrumb language
function updateBreadcrumbLanguage(locale) {
  console.log('=== updateBreadcrumbLanguage called ===', 'locale:', locale);

  const breadcrumbNav = document.getElementById('breadcrumb-nav');
  if (!breadcrumbNav || breadcrumbNav.style.display === 'none') {
    console.log('Breadcrumb not visible, skipping update');
    return;
  }

  // Get the currently active page link to extract language data
  const activeLink = document.querySelector('.page-nav-link.active');
  if (!activeLink) {
    console.log('No active link found, skipping breadcrumb update');
    return;
  }

  const grandparentText = document.querySelector(
    '.breadcrumb-grandparent-text'
  );
  const parentText = document.querySelector('.breadcrumb-parent-text');
  const currentText = document.querySelector('.breadcrumb-current-text');

  // Update grandparent title if it exists
  const grandparentTitleEn = activeLink.getAttribute(
    'data-grandparent-title-en'
  );
  const grandparentTitleFr = activeLink.getAttribute(
    'data-grandparent-title-fr'
  );

  if (grandparentText && grandparentTitleEn) {
    const newGrandparentTitle =
      locale === 'fr' && grandparentTitleFr
        ? grandparentTitleFr
        : grandparentTitleEn;
    console.log(
      'Updating breadcrumb grandparent from:',
      grandparentText.textContent,
      'to:',
      newGrandparentTitle
    );
    grandparentText.textContent = newGrandparentTitle;
  }

  // Update parent title if it exists
  const parentTitleEn = activeLink.getAttribute('data-parent-title-en');
  const parentTitleFr = activeLink.getAttribute('data-parent-title-fr');

  if (parentText && parentTitleEn) {
    const newParentTitle =
      locale === 'fr' && parentTitleFr ? parentTitleFr : parentTitleEn;
    console.log(
      'Updating breadcrumb parent from:',
      parentText.textContent,
      'to:',
      newParentTitle
    );
    parentText.textContent = newParentTitle;
  }

  // Update current title
  const currentTitleEn = activeLink.getAttribute('data-current-title-en');
  const currentTitleFr = activeLink.getAttribute('data-current-title-fr');

  if (currentText && currentTitleEn) {
    const newCurrentTitle =
      locale === 'fr' && currentTitleFr ? currentTitleFr : currentTitleEn;
    console.log(
      'Updating breadcrumb current from:',
      currentText.textContent,
      'to:',
      newCurrentTitle
    );
    currentText.textContent = newCurrentTitle;
  }
}

/**
 * Update the sidebar language select visibility:
 * show only languages that have existing content for the current page.
 * When no page is selected (contentByLocale is null), show all accepted languages.
 */
function updateLanguageSelectOptions(contentByLocale) {
  const select = document.getElementById('sidebar-language-select');
  if (!select) return;

  const currentLocale = getCurrentLocale();

  Array.from(select.options).forEach(option => {
    if (!contentByLocale) {
      option.hidden = false;
    } else {
      const hasContent = contentByLocale[option.value] && contentByLocale[option.value].trim() !== '';
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
 */
function updateTranslationsList(titleByLocale, contentByLocale) {
  document.querySelectorAll('#translations-list .translation-row').forEach(row => {
    const lang = row.getAttribute('data-lang');
    const actionBtn = row.querySelector('.translation-action-btn');
    const titleInput = row.querySelector('.translation-title-input');
    const icon = actionBtn?.querySelector('i');

    const hasContent = contentByLocale?.[lang]?.trim();
    const title = titleByLocale?.[lang] || '';

    if (hasContent) {
      if (icon) icon.className = 'fas fa-pencil-alt text-primary';
      actionBtn?.setAttribute('title', window.journalDetailsData?.translations?.editTranslation || 'Edit');
      if (titleInput) titleInput.value = title;
    } else {
      if (icon) icon.className = 'fas fa-plus text-muted';
      actionBtn?.setAttribute('title', window.journalDetailsData?.translations?.addTranslation || 'Add');
      if (titleInput) titleInput.value = '';
    }
  });
}

/**
 * Reset the translations list to initial empty state (flags stay from Twig).
 */
function resetTranslationsList() {
  document.querySelectorAll('#translations-list .translation-row').forEach(row => {
    const icon = row.querySelector('.translation-action-btn i');
    const titleInput = row.querySelector('.translation-title-input');

    if (icon) icon.className = 'fas fa-plus text-muted';
    if (titleInput) titleInput.value = '';
  });
}

// Make functions globally available for the header script
window.updateInlineEditTranslations = updateInlineEditTranslations;
window.updateContainerTitles = updateContainerTitles;
window.loadTranslations = loadTranslations;

// Translation cache and management
const translationCache = new Map();

// Multi-language fallback translations
function getFallbackTranslations(locale) {
  const translations = {
    fr: {
      edit: 'Éditer',
      editPageContent: 'Éditer le contenu de la page',
      pageTitle: 'Titre de la page',
      content: 'Contenu',
      enterContent: 'Saisissez le contenu ici...',
      cancel: 'Annuler',
      save: 'Sauvegarder',
      selectPageFirst: 'Veuillez sélectionner une page',
      welcomeBackoffice: 'Bienvenue dans le backoffice de gestion du journal',
      missingPageInfo: 'Informations de page manquantes',
      saveSuccess: 'Sauvegardé avec succès',
      saveError: 'Erreur de sauvegarde: ',
      previewPage: 'Aperçu de la page',
    },
    en: {
      edit: 'Edit',
      editPageContent: 'Edit page content',
      pageTitle: 'Page title',
      content: 'Content',
      enterContent: 'Enter content here...',
      cancel: 'Cancel',
      save: 'Save',
      selectPageFirst: 'Please select a page first',
      welcomeBackoffice: 'Welcome to the journal management backoffice',
      missingPageInfo: 'Missing page information',
      saveSuccess: 'Saved successfully',
      saveError: 'Save error: ',
      previewPage: 'Preview Page',
    },
  };

  return translations[locale] || translations.en;
}

// Lazy load translations only when needed
async function loadTranslations(locale) {
  // Check cache first
  if (translationCache.has(locale)) {
    return translationCache.get(locale);
  }

  try {
    const response = await fetch(`/${locale}/api/translations/${locale}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    const translations = await response.json();

    // Cache the translations
    translationCache.set(locale, translations);
    return translations;
  } catch (error) {
    console.error(`Error loading translations for ${locale}:`, error);
    return getFallbackTranslations(locale);
  }
}

// Returns a locale valid for routes (en|fr only)
function getCurrentLocale() {
  const routeLocales = ['en', 'fr'];
  const locale =
    document.documentElement.lang ||
    window.location.pathname.split('/')[1] ||
    'en';

  return routeLocales.includes(locale) ? locale : 'en';
}

// Initialize with current page locale (no API call yet)
function initializeTranslations() {
  const currentLocale = getCurrentLocale();

  // Start with fallback translations based on current locale
  window.translations = getFallbackTranslations(currentLocale);
  window.currentLocale = currentLocale;
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('DOM loaded');

  // Initialize with fallback translations (no API call)
  initializeTranslations();

  // Update UI elements with translations
  updateInlineEditTranslations();

  const pageLinks = document.querySelectorAll('.page-nav-link');
  const homeLink = document.querySelector('a[href*="app_journal_detail"]');
  const pageContent = document.getElementById('page-content');
  const pageViewFields = document.getElementById('page-view-fields');
  const pageHomeContent = document.getElementById('page-home-content');
  const pageTitleView = document.getElementById('page-title-view');
  const pageBody = document.getElementById('page-body');
  const editButton = document.getElementById('edit-button');

  // Preview button elements
  const previewButtonContainer = document.getElementById(
    'preview-page-button-container'
  );
  const previewPageButton = document.getElementById('preview-page-button');

  // Inline edit elements
  const inlineEditContent = document.getElementById('inline-edit-content');
  const pageTitleInline = document.getElementById('page-title-inline');
  const saveInlineButton = document.getElementById('save-inline-edit');
  const cancelInlineButton = document.getElementById('cancel-inline-edit');

  let currentPageCode = null;
  let currentJournalCode = null;
  let isInlineEdit = false;
  let editingLocale = null;

  const sidebarLanguageSelect = document.getElementById('sidebar-language-select');

  if (sidebarLanguageSelect) {
    sidebarLanguageSelect.addEventListener('change', async function () {
      const selectedLang = this.value;
      const routeLocale = getCurrentLocale();

      editingLocale = selectedLang;

      if (currentPageCode && currentJournalCode) {
        const pageUrl = `/${routeLocale}/journal/${currentJournalCode}/page/${currentPageCode}`;

        try {
          const response = await fetch(pageUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
          });

          if (!response.ok) throw new Error('Network response was not ok');

          const data = await response.json();

          updatePageView(data, selectedLang);
          updateTranslationsList(data.title, data.content);

        } catch (error) {
          console.error('Error loading page content:', error);
        }
      }
    });

    // Toggle chevron icon on collapse
    const collapseToggle = document.querySelector('[data-bs-target="#languageWidgetBody"]');
    const languageWidgetBody = document.getElementById('languageWidgetBody');

    if (collapseToggle && languageWidgetBody) {
      languageWidgetBody.addEventListener('shown.bs.collapse', function () {
        const icon = collapseToggle.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-chevron-down');
          icon.classList.add('fa-chevron-up');
        }
      });

      languageWidgetBody.addEventListener('hidden.bs.collapse', function () {
        const icon = collapseToggle.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-chevron-up');
          icon.classList.add('fa-chevron-down');
        }
      });
    }
  }

  // Translation row click: open inline edit for that language
  document.querySelectorAll('#translations-list .translation-action-btn').forEach(btn => {
    btn.addEventListener('click', async function () {
      const row = this.closest('.translation-row');
      const lang = row?.getAttribute('data-lang');

      if (!lang) return;

      if (!currentPageCode || !currentJournalCode) {
        alert(window.translations?.selectPageFirst || 'Please select a page first');
        return;
      }

      if (isInlineEdit) {
        exitInlineEdit();
        await new Promise(r => setTimeout(r, 200));
      }

      // Store the target locale for saving (don't change document.lang / route locale)
      editingLocale = lang;

      // Clear page body and open inline edit with empty fields
      if (pageBody) pageBody.innerHTML = '';

      pageTitleInline.value = '';
      pageContent.style.display = 'none';
      inlineEditContent.style.display = 'block';

      const placeholder = window.translations?.enterContent || 'Enter the content here...';
      try {
        const editorPromise = initializeCKEditor('page-content-inline', placeholder);
        if (editorPromise) {
          await editorPromise;
          setEditorContent('');
          setTimeout(() => focusEditor(), 100);
        }
      } catch (error) {
        console.error('Error initializing CKEditor:', error);
      }

      const cardFooter = document.querySelector('.card-footer');
      if (cardFooter) cardFooter.style.display = 'none';

      isInlineEdit = true;
    });
  });

  // Check if we're on a specific page route on page load
  function initializeCurrentPage() {
    const currentPath = window.location.pathname;
    const pathSegments = currentPath
      .split('/')
      .filter(segment => segment !== '');

    // Check if we're on a page route: /locale/journal/journalCode/page/pageCode
    if (
      pathSegments.length >= 5 &&
      pathSegments[1] === 'journal' &&
      pathSegments[3] === 'page'
    ) {
      const locale = pathSegments[0];
      const journalCode = pathSegments[2];
      const pageCode = pathSegments[4];

      console.log('Detected current page on load:', {
        locale,
        journalCode,
        pageCode,
      });

      // Find and activate the corresponding navigation link
      const correspondingLink = Array.from(pageLinks).find(
        link =>
          link.getAttribute('data-page-code') === pageCode &&
          link.getAttribute('data-journal-code') === journalCode
      );

      if (correspondingLink) {
        // Set current page info
        currentPageCode = pageCode;
        currentJournalCode = journalCode;

        // Activate the link
        pageLinks.forEach(l => l.classList.remove('active'));
        correspondingLink.classList.add('active');

        // Update breadcrumb
        updateBreadcrumb(correspondingLink);

        // Load the page content
        loadCurrentPageContent(locale, journalCode, pageCode);
      }
    }
  }

  function loadCurrentPageContent(locale, journalCode, pageCode) {
    const pageUrl = `/${locale}/journal/${journalCode}/page/${pageCode}`;

    fetch(pageUrl, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          pageBody.innerHTML = '<p class="text-danger">Page not found</p>';
        } else {
          updatePageView(data, locale);
        }
        pageContent.style.display = 'block';

        updateTranslationsList(data.title, data.content);
        updateLanguageSelectOptions(data.content);
      })
      .catch(error => {
        console.error('Error loading current page content:', error);
        pageBody.innerHTML = '<p class="text-danger">Error loading content</p>';
        pageContent.style.display = 'block';
      });
  }

  function updatePageView(data, locale) {
    const noContentText = window.journalDetailsData?.translations?.noContentAvailable || 'No content available';

    if (pageHomeContent) pageHomeContent.style.display = 'none';
    if (pageViewFields) pageViewFields.style.display = 'block';

    if (pageTitleView) {
      pageTitleView.value = (data.title && (data.title[locale] || data.title['en'])) || '';
    }

    if (pageBody) {
      pageBody.innerHTML = (data.content && data.content[locale]) ? data.content[locale] : noContentText;
    }
  }

  function resetToHomeState() {
    if (isInlineEdit) {
      exitInlineEdit();
    }

    currentPageCode = null;
    currentJournalCode = null;

    pageLinks.forEach(l => l.classList.remove('active'));

    hidePreviewButton();

    if (pageViewFields) pageViewFields.style.display = 'none';
    if (pageHomeContent) pageHomeContent.style.display = 'block';

    pageContent.style.display = 'block';

    resetTranslationsList();
    updateLanguageSelectOptions(null);
  }

  // Initialize current page if we're on a page route
  initializeCurrentPage();

  pageLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      console.log('Link clicked');

      const pageCode = this.getAttribute('data-page-code');
      const journalCode = this.getAttribute('data-journal-code');

      console.log('Page code:', pageCode);
      console.log('Journal code:', journalCode);

      if (!pageCode || !journalCode) {
        console.error('Missing pageCode or journalCode');
        return;
      }

      // Exit inline edit mode if active
      if (isInlineEdit) {
        exitInlineEdit();
      }

      // Store current page info for editing
      currentPageCode = pageCode;
      currentJournalCode = journalCode;

      // Remove active class from all links
      pageLinks.forEach(l => l.classList.remove('active'));
      // Add active class to clicked link
      this.classList.add('active');

      // Update breadcrumb
      updateBreadcrumb(this);
      // Extract the locale from the URL, or use the document's locale if it has been changed
      let locale = getCurrentLocale();
      const pageUrl = `/${locale}/journal/${journalCode}/page/${pageCode}`;
      console.log('Fetching:', pageUrl);

      //Update the URL in the browser without reloading
      console.log('Current URL:', window.location.href);
      console.log('New URL will be:', pageUrl);
      history.pushState({}, '', pageUrl);
      console.log('URL after pushState:', window.location.href);

      // Fetch page content via AJAX
      fetch(pageUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      })
        .then(response => {
          console.log('Response status:', response.status);
          return response.json();
        })
        .then(data => {
          console.log('Data received:', JSON.stringify(data, null, 2));
          console.log('Current locale:', locale);
          if (data.error) {
            pageBody.innerHTML = '<p class="text-danger">Page not found</p>';
          } else {
            updatePageView(data, locale);
          }
          pageContent.style.display = 'block';

          updateTranslationsList(data.title, data.content);
          updateLanguageSelectOptions(data.content);
        })
        .catch(error => {
          console.error('Fetch error:', error);
          pageBody.innerHTML =
            '<p class="text-danger">Error loading content</p>';
          pageContent.style.display = 'block';
        });
    });
  });

  // Home link handler - shows welcome content
  if (homeLink) {
    homeLink.addEventListener('click', function (e) {
      e.preventDefault();
      console.log('Home link clicked');

      resetToHomeState();
    });
  }

  // Edit button handler - launches inline edit directly
  editButton.addEventListener('click', function (e) {
    e.preventDefault();
    console.log('Edit button clicked - launching inline edit');

    switchToInlineEdit();
  });

  // Function to switch to inline edit mode
  function switchToInlineEdit() {
    console.log('switchToInlineEdit called');
    console.log('currentPageCode:', currentPageCode);
    console.log('currentJournalCode:', currentJournalCode);

    if (!currentPageCode || !currentJournalCode) {
      console.log('Missing page info - showing alert');
      alert(
        window.translations?.selectPageFirst || 'Please select a page first'
      );
      return;
    }

    const pageTitle = (pageTitleView && pageTitleView.value) || currentPageCode;
    const currentContent = pageBody.innerHTML || '';

    // Populate inline edit form
    pageTitleInline.value = pageTitle;

    // Hide page content and show inline edit
    pageContent.style.display = 'none';
    inlineEditContent.style.display = 'block';

    // Initialize CKEditor
    const placeholder =
      window.translations?.enterContent || 'Enter the content here...';
    console.log('About to initialize CKEditor with placeholder:', placeholder);
    console.log(
      'Target element:',
      document.getElementById('page-content-inline')
    );

    try {
      const editorPromise = initializeCKEditor(
        'page-content-inline',
        placeholder
      );
      console.log('initializeCKEditor returned:', editorPromise);

      if (editorPromise) {
        editorPromise
          .then(() => {
            console.log('CKEditor initialized successfully');
            // Convert the HTML into cleaner content for the editor
            const cleanContent = currentContent
              .replace(/<div class="text-center[^>]*>[\s\S]*?<\/div>/g, '')
              .trim();
            setEditorContent(cleanContent || '');

            // Focus on the editor after a short delay
            setTimeout(() => {
              focusEditor();
            }, 100);
          })
          .catch(error => {
            console.error('Failed to initialize CKEditor:', error);
            // Fallback: create a simple textarea
            const editorElement = document.getElementById(
              'page-content-inline'
            );
            const parentElement = editorElement.parentNode;
            const textarea = document.createElement('textarea');
            textarea.className = 'form-control';
            textarea.id = 'page-content-fallback';
            textarea.rows = 10;
            textarea.placeholder = placeholder;
            textarea.value = currentContent.replace(/<[^>]*>/g, '');

            parentElement.replaceChild(textarea, editorElement);
          });
      } else {
        console.error('initializeCKEditor returned null');
      }
    } catch (error) {
      console.error('Error calling initializeCKEditor:', error);
    }

    // Hide the edit button in footer since we're already in edit mode
    const cardFooter = document.querySelector('.card-footer');
    if (cardFooter) {
      cardFooter.style.display = 'none';
    }

    // Let CSS handle the height naturally - no forced JavaScript heights

    isInlineEdit = true;
  }

  // Function to exit inline edit mode
  function exitInlineEdit() {
    // Destroy CKEditor instance
    destroyEditor().then(() => {
      const fallbackTextarea = document.getElementById('page-content-fallback');
      if (fallbackTextarea) {
        const parentElement = fallbackTextarea.parentNode;
        const originalDiv = document.createElement('div');
        originalDiv.id = 'page-content-inline';
        originalDiv.setAttribute(
          'data-placeholder',
          window.translations?.enterContent || 'Enter the content here...'
        );
        parentElement.replaceChild(originalDiv, fallbackTextarea);
      }

      pageContent.style.display = 'block';
      inlineEditContent.style.display = 'none';

      const cardFooter = document.querySelector('.card-footer');
      if (cardFooter) {
        cardFooter.style.display = 'block';
      }

      isInlineEdit = false;
      // Preserve editingLocale from sidebar language select;
      // reset only if no language is explicitly selected
      if (sidebarLanguageSelect) {
        editingLocale = sidebarLanguageSelect.value;
      }
    });
  }

  // Cancel inline edit handler
  if (cancelInlineButton) {
    cancelInlineButton.addEventListener('click', function () {
      exitInlineEdit();
    });
  }

  // Save inline edit handler
  if (saveInlineButton) {
    saveInlineButton.addEventListener('click', function () {
      console.log('=== SAVE BUTTON CLICKED ===');

      if (!currentPageCode || !currentJournalCode) {
        console.log('ERROR: Missing page info:', {
          currentPageCode,
          currentJournalCode,
        });
        alert(
          window.translations?.missingPageInfo || 'Missing page information'
        );
        return;
      }

      //const newContent = pageContentInline.value;
      const newContent = getEditorContent();
      const newTitle = pageTitleInline.value;

      console.log('Content from editor:', newContent);
      console.log('Title from input:', newTitle);
      console.log('Editor content length:', newContent?.length || 0);

      // editingLocale = target language for translation (e.g. "es"), otherwise current route locale
      let locale = editingLocale || getCurrentLocale();

      const saveUrl = `/${getCurrentLocale()}/journal/${currentJournalCode}/page/${currentPageCode}/edit`;

      console.log('Save URL:', saveUrl);
      console.log('Content locale:', locale);

      // Save via AJAX
      console.log('=== SENDING FETCH REQUEST ===');
      console.log(
        'Request body:',
        JSON.stringify(
          {
            content: newContent,
            title: newTitle,
            locale: locale,
          },
          null,
          2
        )
      );

      fetch(saveUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
          content: newContent,
          title: newTitle,
          locale: locale,
        }),
      })
        .then(response => {
          console.log('=== FETCH RESPONSE RECEIVED ===');
          console.log('Status:', response.status);
          console.log('Status Text:', response.statusText);
          console.log('OK:', response.ok);
          console.log('Headers:', [...response.headers.entries()]);

          return response.json();
        })
        .then(data => {
          console.log('Save response received:', data);
          console.log(
            'HTML content received:',
            data.htmlContent || 'No htmlContent'
          );

          // TEMPORARY DEBUG: Log all response data
          if (data.debug) {
            console.log('=== DEBUG CONVERSION RESULTS ===');
            console.log(
              'Original HTML:',
              data.original_html || 'No original_html'
            );
            console.log(
              'Converted Markdown:',
              data.converted_markdown || 'No converted_markdown'
            );
            console.log(
              'Back to HTML:',
              data.back_to_html || 'No back_to_html'
            );
            console.log(
              'Actual content preview:',
              data.actual_content || 'No actual_content'
            );
            console.log('=== END DEBUG ===');
            return; // Stop here for debug
          }

          if (data.success) {
            const { htmlContent, updatedTitle } = data;
            pageBody.innerHTML = htmlContent || newContent;

            if (updatedTitle && pageTitleView) {
              pageTitleView.value = updatedTitle;
            }

            const activeLink = document.querySelector('.page-nav-link.active');
            if (activeLink && updatedTitle) {
              activeLink.textContent = updatedTitle;

              const currentLocale = getCurrentLocale();

              if (currentLocale === 'fr') {
                activeLink.setAttribute('data-current-title-fr', updatedTitle);
              } else {
                activeLink.setAttribute('data-current-title-en', updatedTitle);
              }

              updateBreadcrumbLanguage(currentLocale);
            }

            // Exit inline edit mode
            exitInlineEdit();

            // Refresh translation list after save
            const routeLocale = getCurrentLocale();
            const refreshUrl = `/${routeLocale}/journal/${currentJournalCode}/page/${currentPageCode}`;
            fetch(refreshUrl, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
              .then(r => r.json())
              .then(pageData => {
                updateTranslationsList(pageData.title, pageData.content);
                updateLanguageSelectOptions(pageData.content);
              })
              .catch(() => {});

            alert(window.translations?.saveSuccess || 'Saved successfully');
          } else {
            alert(
              (window.translations?.saveError || 'Save error: ') +
                (data.message || 'Unknown error')
            );
          }
        })
        .catch(error => {
          console.error('Save error:', error);
          alert(
            (window.translations?.saveError || 'Save error: ') + error.message
          );
        });
    });
  }

  // Function to update breadcrumb navigation
  function updateBreadcrumb(clickedLink) {
    const breadcrumbNav = document.getElementById('breadcrumb-nav');
    const breadcrumbGrandparent = document.getElementById(
      'breadcrumb-grandparent'
    );
    const breadcrumbParent = document.getElementById('breadcrumb-parent');
    const breadcrumbCurrent = document.getElementById('breadcrumb-current');
    const grandparentText = document.querySelector(
      '.breadcrumb-grandparent-text'
    );
    const parentText = document.querySelector('.breadcrumb-parent-text');
    const currentText = document.querySelector('.breadcrumb-current-text');
    const homeLink = document.querySelector('.breadcrumb-home');

    // Get current locale
    const currentLocale = getCurrentLocale();

    // Get page info with locale support
    const pageCode = clickedLink.getAttribute('data-page-code');

    // Get multilingual titles
    const grandparentTitleEn = clickedLink.getAttribute(
      'data-grandparent-title-en'
    );
    const grandparentTitleFr = clickedLink.getAttribute(
      'data-grandparent-title-fr'
    );
    const parentTitleEn = clickedLink.getAttribute('data-parent-title-en');
    const parentTitleFr = clickedLink.getAttribute('data-parent-title-fr');
    const currentTitleEn = clickedLink.getAttribute('data-current-title-en');
    const currentTitleFr = clickedLink.getAttribute('data-current-title-fr');

    // Select title based on current locale
    const grandparentTitle =
      currentLocale === 'fr' && grandparentTitleFr
        ? grandparentTitleFr
        : grandparentTitleEn;
    const parentTitle =
      currentLocale === 'fr' && parentTitleFr ? parentTitleFr : parentTitleEn;
    const currentTitle =
      currentLocale === 'fr' && currentTitleFr
        ? currentTitleFr
        : currentTitleEn || clickedLink.textContent.trim();

    console.log('Updating breadcrumb:', {
      pageCode,
      grandparentTitle,
      parentTitle,
      currentTitle,
      currentLocale,
    });

    // Reset breadcrumb visibility
    breadcrumbGrandparent.style.display = 'none';
    breadcrumbParent.style.display = 'none';
    breadcrumbCurrent.style.display = 'none';

    // Handle home link click
    homeLink.addEventListener('click', function (e) {
      e.preventDefault();
      breadcrumbNav.style.display = 'none';

      resetToHomeState();
    });

    if (grandparentTitle && parentTitle) {
      // Nested page (3 levels): Show grandparent > parent > current
      grandparentText.textContent = grandparentTitle;
      parentText.textContent = parentTitle;
      currentText.textContent = currentTitle;
      breadcrumbGrandparent.style.display = 'block';
      breadcrumbParent.style.display = 'block';
      breadcrumbCurrent.style.display = 'block';
      breadcrumbNav.style.display = 'block';
    } else if (parentTitle) {
      // Sub-page (2 levels): Show parent > current
      parentText.textContent = parentTitle;
      currentText.textContent = currentTitle;
      breadcrumbParent.style.display = 'block';
      breadcrumbCurrent.style.display = 'block';
      breadcrumbNav.style.display = 'block';
    } else {
      // Main page (1 level): Show only current page
      currentText.textContent = currentTitle;
      breadcrumbCurrent.style.display = 'block';
      breadcrumbNav.style.display = 'block';
    }

    // Show preview button and update URL
    updatePreviewButton(pageCode, clickedLink);
  }

  // Function to update preview button
  function updatePreviewButton(pageCode, clickedLink) {
    console.log('DEBUG updatePreviewButton values:');
    console.log('  previewButtonContainer:', previewButtonContainer);
    console.log('  previewPageButton:', previewPageButton);
    console.log('  pageCode:', pageCode);
    console.log('  currentJournalCode:', currentJournalCode);

    console.log('Element search by different methods:');
    console.log(
      '  byId:',
      document.getElementById('preview-page-button-container')
    );
    console.log(
      '  querySelector:',
      document.querySelector('#preview-page-button-container')
    );

    if (!previewButtonContainer) {
      console.error('previewButtonContainer is null! Cannot show button.');
    }
    if (!previewPageButton) {
      console.error('previewPageButton is null! Cannot set URL.');
    }

    if (
      previewButtonContainer &&
      previewPageButton &&
      currentJournalCode &&
      clickedLink
    ) {
      let previewUrl;

      // Check if this is a container page (has children)
      const isContainer =
        clickedLink.getAttribute('data-is-container') === 'true';

      if (isContainer) {
        // For container pages, use the container title to create the URL
        const currentLocale = getCurrentLocale();
        const titleEn = clickedLink.getAttribute('data-title-en');
        const titleFr = clickedLink.getAttribute('data-title-fr');
        const title = currentLocale === 'fr' && titleFr ? titleFr : titleEn;

        // Convert title to URL-friendly format (lowercase, spaces to hyphens, remove special chars)
        const urlTitle = title
          ? title
              .toLowerCase()
              .replace(/\s+/g, '-')
              .replace(/[^\w-]/g, '')
          : 'page';
        previewUrl = `https://${currentJournalCode}.episciences.org/${urlTitle}`;
        console.log(
          'DEBUG: Container page detected, using title:',
          title,
          '→',
          urlTitle
        );
      } else {
        // Check if this is a child page (has parent title data)
        const parentTitleEn = clickedLink.getAttribute('data-parent-title-en');
        const parentTitleFr = clickedLink.getAttribute('data-parent-title-fr');

        if (parentTitleEn || parentTitleFr) {
          // This is a child page, redirect to parent page
          const currentLocale = getCurrentLocale();
          const parentTitle =
            currentLocale === 'fr' && parentTitleFr
              ? parentTitleFr
              : parentTitleEn;
          const urlTitle = parentTitle
            ? parentTitle
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]/g, '')
            : 'page';
          previewUrl = `https://${currentJournalCode}.episciences.org/${urlTitle}`;
          console.log(
            'DEBUG: Child page detected, redirecting to parent:',
            parentTitle,
            '→',
            urlTitle
          );
        } else if (pageCode) {
          // For regular pages, use pageCode
          previewUrl = `https://${currentJournalCode}.episciences.org/${pageCode}`;
          console.log(
            'DEBUG: Regular page detected, using pageCode:',
            pageCode
          );
        } else {
          // No pageCode available, hide preview button
          console.log('DEBUG: No pageCode available, hiding preview button');
          previewButtonContainer.style.display = 'none';
          return;
        }
      }

      // Update button href and show it
      previewPageButton.href = previewUrl;
      previewButtonContainer.style.display = 'block';

      // Update button text with current translation
      if (window.translations && window.translations.previewPage) {
        previewPageButton.innerHTML =
          '<i class="fas fa-external-link-alt me-1"></i>' +
          window.translations.previewPage;
      }

      console.log('DEBUG: Preview button shown with URL:', previewUrl);
    } else {
      // Hide preview button if no page is selected
      if (previewButtonContainer) {
        previewButtonContainer.style.display = 'none';
        console.log('DEBUG: Preview button hidden - condition failed');
      }
    }
  }

  // Function to hide preview button when going to home
  function hidePreviewButton() {
    if (previewButtonContainer) {
      previewButtonContainer.style.display = 'none';
    }
  }
});
