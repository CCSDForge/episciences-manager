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

  const parentText = document.querySelector('.breadcrumb-parent-text');
  const currentText = document.querySelector('.breadcrumb-current-text');

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

// Helper function to get current locale consistently
function getCurrentLocale() {
  const locale =
    document.documentElement.lang ||
    window.location.pathname.split('/')[1] ||
    'en';

  // Validate the locale (only EN and FR are supported)
  return ['en', 'fr'].includes(locale) ? locale : 'en';
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

  const pageLinks = document.querySelectorAll('.page-nav-link');
  const homeLink = document.querySelector('a[href*="app_journal_detail"]');
  const pageContent = document.getElementById('page-content');
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

  console.log('Found links:', pageLinks.length);
  console.log('Page content element:', pageContent);

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

  // Function to load current page content
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
          pageBody.innerHTML =
            data.content[locale] ||
            data.content['en'] ||
            'No content available';
        }
        pageContent.style.display = 'block';
      })
      .catch(error => {
        console.error('Error loading current page content:', error);
        pageBody.innerHTML = '<p class="text-danger">Error loading content</p>';
        pageContent.style.display = 'block';
      });
  }

  // Function to reset to home state
  function resetToHomeState() {
    // Exit inline edit mode if active
    if (isInlineEdit) {
      exitInlineEdit();
    }

    // Clear current page info
    currentPageCode = null;
    currentJournalCode = null;

    // Remove active class from all page links
    pageLinks.forEach(l => l.classList.remove('active'));

    // Hide preview button
    hidePreviewButton();

    // Show default home content
    pageBody.innerHTML = `
      <div class="text-center py-5">
        <i class="fas fa-home fa-3x text-primary mb-3"></i>
        <h3>${window.translations?.welcomeBackoffice || 'Welcome to the journal management backoffice'}</h3>
        <p class="text-muted">${window.translations?.selectPageFirst || 'Please select a page to edit first'}</p>
      </div>
    `;
    pageContent.style.display = 'block';
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
          console.log(
            'Raw content from server:',
            JSON.stringify(data.content, null, 2)
          );
          console.log('Current locale:', locale);
          if (data.error) {
            pageBody.innerHTML = '<p class="text-danger">Page not found</p>';
          } else {
            // Use the locale extracted from the URL
            pageBody.innerHTML =
              data.content[locale] ||
              data.content['en'] ||
              'No content available';
            console.log('Content to show:', pageBody.innerHTML);
            //the content is HTML converted from markdown, so it's safe to use innerHTML
          }
          pageContent.style.display = 'block';
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

    // Get current page title and content
    const activeLink = document.querySelector('.page-nav-link.active');
    const pageTitle = activeLink
      ? activeLink.textContent.trim()
      : currentPageCode;
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

      // Show the edit button footer again
      const cardFooter = document.querySelector('.card-footer');
      if (cardFooter) {
        cardFooter.style.display = 'block';
      }

      isInlineEdit = false;
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
      let locale = getCurrentLocale();

      const saveUrl = `/${locale}/journal/${currentJournalCode}/page/${currentPageCode}/edit`;

      console.log('Saving content:', newContent);
      console.log('Save URL:', saveUrl);
      console.log('Locale:', locale);

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
            // Update page content
            const { htmlContent, updatedTitle } = data;
            pageBody.innerHTML = htmlContent || newContent;

            // Update page title in navigation and breadcrumb data
            const activeLink = document.querySelector('.page-nav-link.active');
            if (activeLink && updatedTitle) {
              // Update the visible text
              activeLink.textContent = updatedTitle;

              // Update the data attributes used by breadcrumb
              const currentLocale = getCurrentLocale();

              if (currentLocale === 'fr') {
                activeLink.setAttribute('data-current-title-fr', updatedTitle);
              } else {
                activeLink.setAttribute('data-current-title-en', updatedTitle);
              }

              // Update breadcrumb immediately if it's visible
              updateBreadcrumbLanguage(currentLocale);
            }

            // Exit inline edit mode
            exitInlineEdit();

            // Show success message
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
    const breadcrumbParent = document.getElementById('breadcrumb-parent');
    const breadcrumbCurrent = document.getElementById('breadcrumb-current');
    const parentText = document.querySelector('.breadcrumb-parent-text');
    const currentText = document.querySelector('.breadcrumb-current-text');
    const homeLink = document.querySelector('.breadcrumb-home');

    // Get current locale
    const currentLocale = getCurrentLocale();

    // Get page info with locale support
    const pageCode = clickedLink.getAttribute('data-page-code');

    // Get multilingual titles
    const parentTitleEn = clickedLink.getAttribute('data-parent-title-en');
    const parentTitleFr = clickedLink.getAttribute('data-parent-title-fr');
    const currentTitleEn = clickedLink.getAttribute('data-current-title-en');
    const currentTitleFr = clickedLink.getAttribute('data-current-title-fr');

    // Select title based on current locale
    const parentTitle =
      currentLocale === 'fr' && parentTitleFr ? parentTitleFr : parentTitleEn;
    const currentTitle =
      currentLocale === 'fr' && currentTitleFr
        ? currentTitleFr
        : currentTitleEn || clickedLink.textContent.trim();

    console.log('Updating breadcrumb:', {
      pageCode,
      parentTitle,
      currentTitle,
      currentLocale,
    });

    // Reset breadcrumb visibility
    breadcrumbParent.style.display = 'none';
    breadcrumbCurrent.style.display = 'none';

    // Handle home link click
    homeLink.addEventListener('click', function (e) {
      e.preventDefault();
      breadcrumbNav.style.display = 'none';

      resetToHomeState();
    });

    if (parentTitle) {
      // Sub-page: Show breadcrumb with parent
      parentText.textContent = parentTitle;
      currentText.textContent = currentTitle;
      breadcrumbParent.style.display = 'block';
      breadcrumbCurrent.style.display = 'block';
      breadcrumbNav.style.display = 'block';
    } else {
      // Main page: Show only current page
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
