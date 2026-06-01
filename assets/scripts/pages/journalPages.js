import { initLanguageWidget } from '../components/language-widget.js';
import { Collapse } from 'bootstrap';
import {
  initializeCKEditor,
  getEditorContent,
  setEditorContent,
} from '../components/ckeditor.js';

// Security: Helper function to escape HTML special characters to prevent XSS
function escapeHtml(text) {
  if (text === null || text === undefined) {
    return '';
  }
  const div = document.createElement('div');
  div.textContent = String(text);
  return div.innerHTML;
}

/**
 * Journal Pages - Simplified form-based editing
 *
 * This module handles:
 * 1. Language widget for the edit form
 * 2. CKEditor initialization when form opens
 * 3. Syncing translations to hidden inputs before submit
 * 4. Mobile menu handling
 */

document.addEventListener('DOMContentLoaded', () => {
  // ===================
  // CONFIGURATION
  // ===================

  const config = window.pageConfig;
  if (!config) {
    console.error('pageConfig is not defined');
    return;
  }

  // ===================
  // ELEMENT GETTERS
  // ===================

  const getElement = id => document.getElementById(id);
  const getTitleInput = () => getElement('page_title');
  const getContentInput = () => getElement('page_content');
  const getFormLanguageInput = () => getElement('form-language');

  // ===================
  // TRANSLATIONS STORAGE
  // ===================

  const translations = {};
  // Check URL parameter first, then sessionStorage as fallback
  const urlParams = new URLSearchParams(window.location.search);
  const urlContentLang = urlParams.get('contentLang');

  // Fallback: check sessionStorage (for backward compatibility)
  const sessionContentLang = sessionStorage.getItem('editContentLanguage');
  if (sessionContentLang) {
    sessionStorage.removeItem('editContentLanguage'); // Use only once
  }

  // Priority: URL param > sessionStorage > contentLanguage (after save) > interface locale
  let currentLang =
    urlContentLang && config.acceptedLanguages.includes(urlContentLang)
      ? urlContentLang
      : sessionContentLang &&
          config.acceptedLanguages.includes(sessionContentLang)
        ? sessionContentLang
        : config.contentLanguage &&
            config.acceptedLanguages.includes(config.contentLanguage)
          ? config.contentLanguage
          : config.acceptedLanguages.includes(config.locale)
            ? config.locale
            : config.defaultLanguage;
  let sidebarWidget = null;
  let editorInitialized = false;
  let formIsOpen = false;

  // Initialize translations from pageData config or hidden inputs
  function initTranslations() {
    config.acceptedLanguages.forEach(lang => {
      // First try to get from pageData (passed from server)
      if (config.pageData) {
        translations[lang] = {
          title: config.pageData.title?.[lang] || '',
          // CKEditor with Markdown plugin expects Markdown input
          content: config.pageData.markdownContent?.[lang] || '',
        };
      } else {
        // Fallback to hidden inputs
        const titleHidden = getElement(`translation-title-${lang}`);
        const contentHidden = getElement(`translation-content-${lang}`);

        translations[lang] = {
          title: titleHidden?.value || '',
          content: contentHidden?.value || '',
        };
      }
    });
  }

  // ===================
  // CORE FUNCTIONS
  // ===================

  function saveCurrentLanguage() {
    if (!currentLang) return;

    translations[currentLang] = {
      title: getTitleInput()?.value || '',
      content: editorInitialized
        ? getEditorContent()
        : getContentInput()?.textContent || '',
    };

    updateHiddenInputs(currentLang);
  }

  function loadLanguage(lang) {
    currentLang = lang;
    const data = translations[lang] || { title: '', content: '' };
    const defaultData = translations[config.defaultLanguage] || {
      title: '',
      content: '',
    };

    const titleInput = getTitleInput();
    const formLanguageInput = getFormLanguageInput();

    // Update title with fallback to default language (title is read-only, comes from YAML)
    if (titleInput) {
      titleInput.value = data.title || defaultData.title || '';
    }
    if (formLanguageInput) formLanguageInput.value = lang;

    // Update content - NO fallback, show empty if no content for this language
    // This makes it clear to the editor that content needs to be added
    if (editorInitialized) {
      setEditorContent(data.content || '');
    }
  }

  function updateHiddenInputs(lang) {
    const titleHidden = getElement(`translation-title-${lang}`);
    const contentHidden = getElement(`translation-content-${lang}`);

    if (titleHidden) titleHidden.value = translations[lang]?.title || '';
    if (contentHidden) contentHidden.value = translations[lang]?.content || '';
  }

  // Update widget translations display
  function updateWidgetDisplay() {
    if (!sidebarWidget) return;

    const titleByLocale = {};
    const contentByLocale = {};

    config.acceptedLanguages.forEach(lang => {
      titleByLocale[lang] = translations[lang]?.title || '';
      contentByLocale[lang] = translations[lang]?.content || '';
    });

    sidebarWidget.updateTranslations(titleByLocale, contentByLocale, {});
    // For pages: show only languages with content in dropdown
    sidebarWidget.updateOptions(contentByLocale);
  }

  // ===================
  // LANGUAGE CHANGE HANDLER
  // ===================

  function handleLanguageChange(selectedLang) {
    if (formIsOpen) {
      // When form is open, switch translations in editor
      saveCurrentLanguage();
      loadLanguage(selectedLang);
    } else {
      // When form is closed, update the view content (no redirect)
      updateViewContent(selectedLang);
    }
    currentLang = selectedLang;
  }

  // Update the page view content based on selected language
  function updateViewContent(lang) {
    const titleElement = document.getElementById('page-view-title');
    const contentElement = document.getElementById('page-view-content');

    if (!config.pageData || !titleElement || !contentElement) return;

    // Title: keep fallback (titles come from YAML and are usually translated)
    const title =
      config.pageData.title?.[lang] ||
      config.pageData.title?.[config.defaultLanguage] ||
      '';

    // Content: NO fallback - show "no content" message if empty for this language
    const content = config.pageData.content?.[lang] || '';

    // Update title
    if (title) {
      titleElement.textContent = title;
    }

    // Update content - show message if no content for selected language
    if (content) {
      contentElement.innerHTML = content;
    } else {
      const noContentMsg =
        config.translations?.noContentAvailable || 'No content available';
      contentElement.innerHTML = `<p class="text-muted fst-italic no-content-message">${escapeHtml(noContentMsg)}</p>`;
    }
  }

  // Update URL parameter without reloading page
  function updateUrlContentLang(lang) {
    const url = new URL(window.location.href);
    url.searchParams.set('contentLang', lang);
    window.history.replaceState({}, '', url.toString());
  }

  function handleTranslationClick(lang) {
    const pageEditForm = getElement('pageEditForm');

    if (config.editMode || formIsOpen) {
      // Form is already open (edit mode or collapse open), just switch language
      saveCurrentLanguage();
      loadLanguage(lang);
      if (sidebarWidget?.select) {
        sidebarWidget.select.value = lang;
      }
      // Update URL to reflect current language
      updateUrlContentLang(lang);
    } else if (pageEditForm) {
      // Form exists as collapse, open it and switch to the requested language
      currentLang = lang;
      const bsCollapse = new Collapse(pageEditForm, { toggle: false });
      bsCollapse.show();
      // The shown.bs.collapse event will handle the rest
    } else if (config.currentPage) {
      // View mode: form not in DOM, redirect to edit page
      // Pass language as URL parameter
      const editUrl = `/${config.locale}/journal/${config.journalCode}/pages/${config.currentPage}/edit?contentLang=${lang}`;
      window.location.href = editUrl;
    }
  }

  // ===================
  // CKEDITOR INITIALIZATION
  // ===================

  async function initPageEditor() {
    if (editorInitialized) return;

    const placeholder = 'Enter page content...';
    try {
      await initializeCKEditor('page_content', placeholder, content => {
        if (!translations[currentLang]) {
          translations[currentLang] = { title: '', content: '' };
        }
        translations[currentLang].content = content;
        updateWidgetDisplay();
      });
      editorInitialized = true;

      // Load current language content into editor - NO fallback, show empty if no content
      const currentContent = translations[currentLang]?.content || '';
      setEditorContent(currentContent);
    } catch (error) {
      console.error('Failed to initialize CKEditor:', error);
    }
  }

  // ===================
  // FORM HANDLERS
  // ===================

  // In edit mode, form is already open (not a collapse)
  if (config.editMode) {
    formIsOpen = true;
    initTranslations();
    // Note: sidebarWidget is not yet initialized here, updateWidgetDisplay will be called later
  }

  // ===================
  // EVENT LISTENERS
  // ===================

  // Real-time update on input
  document.addEventListener('input', e => {
    if (!translations[currentLang]) {
      translations[currentLang] = { title: '', content: '' };
    }

    if (e.target.id === 'page_title') {
      translations[currentLang].title = e.target.value;
      updateWidgetDisplay();
    }
  });

  // Save all translations before form submit
  const pageForm = getElement('page-form');
  if (pageForm) {
    pageForm.addEventListener('submit', () => {
      saveCurrentLanguage();
      config.acceptedLanguages.forEach(lang => updateHiddenInputs(lang));
    });
  }

  // ===================
  // MOBILE MENU HANDLING
  // ===================

  // Close mobile menu when clicking on a page link
  const mobileMenuCheckbox = getElement('mobile-menu-toggle');
  if (mobileMenuCheckbox) {
    document
      .querySelectorAll('.page-nav-link, .home-nav-link')
      .forEach(link => {
        link.addEventListener('click', () => {
          mobileMenuCheckbox.checked = false;
        });
      });
  }

  // ===================
  // SIDEBAR LANGUAGE WIDGET
  // ===================

  sidebarWidget = initLanguageWidget({
    widgetId: 'sidebar',
    onLanguageChange: handleLanguageChange,
    onTranslationClick: handleTranslationClick,
    iconBasedOnContentOnly: true, // For pages: icon based on content only (titles come from YAML)
  });

  // ===================
  // INITIALIZE
  // ===================

  initTranslations();
  // Update widget display on page load to show correct icons (+ or pencil)
  updateWidgetDisplay();

  // Synchronize widget dropdown with currentLang on page load
  // This ensures the widget shows the same language as the displayed content
  if (sidebarWidget?.select) {
    sidebarWidget.select.value = currentLang;
  }

  // Update content based on interface language at page load
  const titleInputOnLoad = getTitleInput();
  const contentInputOnLoad = getContentInput();

  if (config.editMode && (titleInputOnLoad || contentInputOnLoad)) {
    // Edit mode: update form elements
    const data = translations[currentLang] || { title: '', content: '' };
    const defaultData = translations[config.defaultLanguage] || {
      title: '',
      content: '',
    };

    // Immediately update title with correct language (before CKEditor loads)
    if (titleInputOnLoad) {
      titleInputOnLoad.value = data.title || defaultData.title || '';
    }

    // Note: Don't update content div here - CKEditor will handle it via setEditorContent()
    // Using textContent would insert raw text instead of HTML

    // Ensure formIsOpen is true for other handlers
    formIsOpen = true;

    // Then initialize CKEditor and reload content
    (async () => {
      await initPageEditor();
      loadLanguage(currentLang);
    })();
  } else if (!config.editMode && config.currentPage) {
    // View mode: update view elements to match interface language
    updateViewContent(currentLang);
  }
});
