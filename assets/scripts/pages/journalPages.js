import { initLanguageWidget } from '../components/language-widget.js';
import { Collapse } from 'bootstrap';
import {
  initializeCKEditor,
  getEditorContent,
  setEditorContent,
  destroyEditor,
} from '../components/ckeditor.js';

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
  let currentLang = config.defaultLanguage;
  let formWidget = null;
  let editorInitialized = false;

  // Initialize translations from hidden inputs
  function initTranslations() {
    config.acceptedLanguages.forEach(lang => {
      const titleHidden = getElement(`translation-title-${lang}`);
      const contentHidden = getElement(`translation-content-${lang}`);

      translations[lang] = {
        title: titleHidden?.value || '',
        content: contentHidden?.value || '',
      };
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

    const titleInput = getTitleInput();
    const formLanguageInput = getFormLanguageInput();

    if (titleInput) titleInput.value = data.title;
    if (formLanguageInput) formLanguageInput.value = lang;

    // Update CKEditor content
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
  function updateFormWidgetDisplay() {
    if (!formWidget) return;

    const titleByLocale = {};
    const contentByLocale = {};
    const hasDataByLocale = {};

    config.acceptedLanguages.forEach(lang => {
      titleByLocale[lang] = translations[lang]?.title || '';
      contentByLocale[lang] = translations[lang]?.content || '';
      hasDataByLocale[lang] = titleByLocale[lang] || contentByLocale[lang];
    });

    formWidget.updateTranslations(titleByLocale, contentByLocale, {});
    formWidget.updateOptions(hasDataByLocale);
  }

  // ===================
  // INITIALIZE FORM WIDGET
  // ===================

  function initFormWidget() {
    if (formWidget) return;

    formWidget = initLanguageWidget({
      widgetId: 'page-form',
      onLanguageChange: selectedLang => {
        saveCurrentLanguage();
        loadLanguage(selectedLang);
      },
      onTranslationClick: lang => {
        saveCurrentLanguage();
        loadLanguage(lang);
        if (formWidget?.select) {
          formWidget.select.value = lang;
        }
      },
    });

    if (formWidget) {
      initTranslations();
      updateFormWidgetDisplay();
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
        updateFormWidgetDisplay();
      });
      editorInitialized = true;

      // Load current language content into editor
      const currentContent = translations[currentLang]?.content || '';
      setEditorContent(currentContent);
    } catch (error) {
      console.error('Failed to initialize CKEditor:', error);
    }
  }

  function destroyPageEditor() {
    if (editorInitialized) {
      destroyEditor();
      editorInitialized = false;
    }
  }

  // ===================
  // FORM COLLAPSE HANDLERS
  // ===================

  const pageEditForm = getElement('pageEditForm');
  if (pageEditForm) {
    pageEditForm.addEventListener('shown.bs.collapse', async () => {
      initFormWidget();
      await initPageEditor();
    });

    pageEditForm.addEventListener('hidden.bs.collapse', () => {
      destroyPageEditor();
    });

    // If form should be open on load (editMode=true)
    if (config.editMode && pageEditForm) {
      const bsCollapse = new Collapse(pageEditForm, { toggle: false });
      bsCollapse.show();
    }
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
      updateFormWidgetDisplay();
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
    document.querySelectorAll('.page-nav-link, .home-nav-link').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenuCheckbox.checked = false;
      });
    });
  }

  // ===================
  // SIDEBAR LANGUAGE WIDGET (for viewing)
  // ===================

  initLanguageWidget({
    widgetId: 'sidebar',
    onLanguageChange: selectedLang => {
      // Change the page locale by redirecting
      const currentUrl = new URL(window.location.href);
      const pathParts = currentUrl.pathname.split('/');

      // Find and replace locale in path (assuming /{locale}/journal/...)
      if (pathParts.length > 1 && ['en', 'fr'].includes(pathParts[1])) {
        pathParts[1] = selectedLang;
        currentUrl.pathname = pathParts.join('/');
        window.location.href = currentUrl.toString();
      }
    },
  });

  // ===================
  // INITIALIZE
  // ===================

  initTranslations();
});