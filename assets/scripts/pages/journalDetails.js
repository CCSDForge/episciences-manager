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
    window.translations ? Object.keys(window.translations) : 'none',
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
      window.translations.editPageContent,
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
      inlineEditTitle.innerHTML,
    );
  }

  // Update page title label (inline edit)
  const pageTitleLabel = document.querySelector(
    'label[for="page-title-inline"]',
  );
  console.log('Page title label found:', !!pageTitleLabel);
  if (pageTitleLabel && window.translations.pageTitle) {
    console.log(
      'Updating page title label from:',
      pageTitleLabel.textContent,
      'to:',
      window.translations.pageTitle,
    );
    pageTitleLabel.textContent = window.translations.pageTitle;
  }

  // Update content label (inline edit)
  const contentLabel = document.querySelector(
    'label[for="page-content-inline"]',
  );
  console.log('Content label found:', !!contentLabel);
  if (contentLabel && window.translations.content) {
    console.log(
      'Updating content label from:',
      contentLabel.textContent,
      'to:',
      window.translations.content,
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
      window.translations.enterContent,
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

// Make functions globally available for the header script
window.updateInlineEditTranslations = updateInlineEditTranslations;
window.loadTranslations = loadTranslations;

// Translation cache and management
const translationCache = new Map();
const fallbackTranslations = {
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
};

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
    return fallbackTranslations;
  }
}

// Initialize with current page locale (no API call yet)
function initializeTranslations() {
  const currentLocale =
    document.documentElement.lang ||
    window.location.pathname.split('/')[1] ||
    'en';

  // Start with fallback, load via API only when language changes
  window.translations = fallbackTranslations;
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
      // Extract the locale from the URL, or use the document's locale if it has been changed
      let locale =
        document.documentElement.lang ||
        window.location.pathname.split('/')[1] ||
        'en';
      // Validate the locale
      if (locale !== 'en' && locale !== 'fr') {
        locale = 'en';
      }
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
            JSON.stringify(data.content, null, 2),
          );
          console.log('Current locale:', locale);
          if (data.error) {
            pageBody.innerHTML = '<p class="text-danger">Page not found</p>';
          } else {
            // Use the locale extracted from the URL
            const contentToShow =
              data.content[locale] ||
              data.content['en'] ||
              'No content available';
            console.log('Content to show:', contentToShow);
            //the content is HTML converted from markdown, so it's safe to use innerHTML
            pageBody.innerHTML = contentToShow;
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

      // Exit inline edit mode if active
      if (isInlineEdit) {
        exitInlineEdit();
      }

      // Clear current page info
      currentPageCode = null;
      currentJournalCode = null;

      // Remove active class from all page links
      pageLinks.forEach(l => l.classList.remove('active'));

      // Show default home content
      pageBody.innerHTML = `
        <div class="text-center py-5">
          <i class="fas fa-home fa-3x text-primary mb-3"></i>
          <h3>${window.translations?.welcomeBackoffice || 'Welcome to the journal management backoffice'}</h3>
          <p class="text-muted">${window.translations?.selectPageFirst || 'Please select a page to edit first'}</p>
        </div>
      `;
      pageContent.style.display = 'block';
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
        window.translations?.selectPageFirst || 'Please select a page first',
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
      document.getElementById('page-content-inline'),
    );

    try {
      const editorPromise = initializeCKEditor(
        'page-content-inline',
        placeholder,
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
              'page-content-inline',
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
          window.translations?.enterContent || 'Enter the content here...',
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
      if (!currentPageCode || !currentJournalCode) {
        alert(
          window.translations?.missingPageInfo || 'Missing page information',
        );
        return;
      }

      //const newContent = pageContentInline.value;
      const newContent = getEditorContent();
      const newTitle = pageTitleInline.value;
      let locale =
        document.documentElement.lang ||
        window.location.pathname.split('/')[1] ||
        'en';

      if (locale !== 'en' && locale !== 'fr') {
        locale = 'en';
      }

      const saveUrl = `/${locale}/journal/${currentJournalCode}/page/${currentPageCode}/edit`;

      console.log('Saving content:', newContent);
      console.log('Save URL:', saveUrl);
      console.log('Locale:', locale);

      // Save via AJAX
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
        .then(response => response.json())
        .then(data => {
          console.log('Save response received:', data);
          console.log('HTML content received:', data.htmlContent);

          if (data.success) {
            // Update page content
            pageBody.innerHTML = data.htmlContent || newContent;

            // Update page title in navigation
            const activeLink = document.querySelector('.page-nav-link.active');
            if (activeLink && data.updatedTitle) {
              activeLink.textContent = data.updatedTitle;
            }

            // Exit inline edit mode
            exitInlineEdit();

            // Show success message
            alert(window.translations?.saveSuccess || 'Saved successfully');
          } else {
            alert(
              (window.translations?.saveError || 'Save error: ') +
                (data.message || 'Unknown error'),
            );
          }
        })
        .catch(error => {
          console.error('Save error:', error);
          alert(
            (window.translations?.saveError || 'Save error: ') + error.message,
          );
        });
    });
  }
});
