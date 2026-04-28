import { initLanguageWidget } from '../components/language-widget.js';
import { Collapse, Modal } from 'bootstrap';
import {
  initializeCKEditor,
  getEditorContent,
  setEditorContent,
  destroyEditor,
  isOverLimit,
} from '../components/ckeditor';
document.addEventListener('DOMContentLoaded', () => {
  // ===================
  // CONFIGURATION
  // ===================

  const config = window.newsLanguageConfig;
  if (!config) {
    console.error('newsLanguageConfig is not defined');
    return;
  }

  // ===================
  // ELEMENT GETTERS
  // ===================

  const getElement = id => document.getElementById(id);
  const getTitleInput = () => getElement('news_title');
  const getContentInput = () => getElement('news_content');
  const getLinkInput = () => getElement('news_link');
  const getFormLanguageInput = () => getElement('form-language');

  // ===================
  // TRANSLATIONS STORAGE
  // ===================

  const translations = {};
  let currentLang = config.defaultLanguage;
  let formWidget = null;
  //Ajouter une variable pour l'état de l'éditeur
  let editorInitialized = false;

  // Initialize translations from hidden inputs
  function initTranslations() {
    config.acceptedLanguages.forEach(lang => {
      const titleHidden = getElement(`translation-title-${lang}`);
      const contentHidden = getElement(`translation-content-${lang}`);
      const linkHidden = getElement(`translation-link-${lang}`);

      translations[lang] = {
        title: titleHidden?.value || '',
        content: contentHidden?.value || '',
        link: linkHidden?.value || '',
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
        : getContentInput()?.value || '',
      link: getLinkInput()?.value || '',
    };

    updateHiddenInputs(currentLang);
  }

  function loadLanguage(lang) {
    currentLang = lang;
    const data = translations[lang] || { title: '', content: '', link: '' };

    const titleInput = getTitleInput();
    const contentInput = getContentInput();
    const linkInput = getLinkInput();
    const formLanguageInput = getFormLanguageInput();

    if (titleInput) titleInput.value = data.title;
    if (contentInput) contentInput.value = data.content;
    if (linkInput) linkInput.value = data.link;
    if (formLanguageInput) formLanguageInput.value = lang;

    // Update CKEditor content
    if (editorInitialized) {
      setEditorContent(data.content || '');
    } else {
      const contentInput = getContentInput();
      if (contentInput) contentInput.value = data.content;
    }
  }

  function updateHiddenInputs(lang) {
    const titleHidden = getElement(`translation-title-${lang}`);
    const contentHidden = getElement(`translation-content-${lang}`);
    const linkHidden = getElement(`translation-link-${lang}`);

    if (titleHidden) titleHidden.value = translations[lang]?.title || '';
    if (contentHidden) contentHidden.value = translations[lang]?.content || '';
    if (linkHidden) linkHidden.value = translations[lang]?.link || '';
  }

  // Update widget translations display
  function updateFormWidgetDisplay() {
    if (!formWidget) return;

    const titleByLocale = {};
    const contentByLocale = {};

    config.acceptedLanguages.forEach(lang => {
      titleByLocale[lang] = translations[lang]?.title || '';
      contentByLocale[lang] = translations[lang]?.content || '';
    });

    formWidget.updateTranslations(titleByLocale, contentByLocale, {});
    formWidget.updateOptions(contentByLocale);
  }

  // ===================
  // INITIALIZE FORM WIDGET (on collapse show)
  // ===================

  function initFormWidget() {
    if (formWidget) return; // Already initialized

    formWidget = initLanguageWidget({
      widgetId: 'news-form',
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

  const NEWS_CONTENT_MAX_LENGTH = config.contentMaxLength || 5000;

  async function initNewsEditor() {
    if (editorInitialized) return;

    const placeholder = 'Enter news content...';
    try {
      // Pass onChange callback to update translations when content changes
      // Pass maxLength for character counter (5000 for news)
      await initializeCKEditor('news_content', placeholder, (content) => {
        if (!translations[currentLang]) {
          translations[currentLang] = { title: '', content: '', link: '' };
        }
        translations[currentLang].content = content;
        updateFormWidgetDisplay();
      }, NEWS_CONTENT_MAX_LENGTH);
      editorInitialized = true;

      // Load current language content into editor
      const currentContent = translations[currentLang]?.content || '';
      setEditorContent(currentContent);
    } catch (error) {
      console.error('Failed to initialize CKEditor:', error);
    }
  }

  function destroyNewsEditor() {
    if (editorInitialized) {
      destroyEditor();
      editorInitialized = false;
    }
  }

  // ===================
  // RESET FORM FOR CREATE MODE
  // ===================

  function resetFormForCreate() {
    // Clear translations object
    config.acceptedLanguages.forEach(lang => {
      translations[lang] = { title: '', content: '', link: '' };

      // Clear hidden inputs
      const titleHidden = getElement(`translation-title-${lang}`);
      const contentHidden = getElement(`translation-content-${lang}`);
      const linkHidden = getElement(`translation-link-${lang}`);

      if (titleHidden) titleHidden.value = '';
      if (contentHidden) contentHidden.value = '';
      if (linkHidden) linkHidden.value = '';
    });

    // Clear visible form fields
    const titleInput = getTitleInput();
    const contentInput = getContentInput();
    const linkInput = getLinkInput();

    if (titleInput) titleInput.value = '';
    if (contentInput) contentInput.value = '';
    if (linkInput) linkInput.value = '';

    // Reset status to default (invisible)
    const statusSelect = getElement('news_status');
    if (statusSelect) statusSelect.value = 'invisible';

    // Reset form action to create URL
    const form = getElement('news-form');
    const defaultCreateUrl = getElement('default-create-url');
    if (form && defaultCreateUrl) {
      form.action = defaultCreateUrl.value;
    }

    // Reset CSRF token to create token
    const tokenInput = form?.querySelector('input[name="_token"]');
    const createToken = getElement('csrf-token-create');
    if (tokenInput && createToken) {
      tokenInput.value = createToken.value;
    }

    // Clear CKEditor content if initialized
    if (editorInitialized) {
      setEditorContent('');
    }

    // Update widget display
    updateFormWidgetDisplay();
  }

  // Initialize when collapse is shown
  const newsCreateForm = getElement('newsCreateForm');
  if (newsCreateForm) {
    newsCreateForm.addEventListener('shown.bs.collapse', async () => {
      initFormWidget();
      await initNewsEditor();
    });

    newsCreateForm.addEventListener('hidden.bs.collapse', () => {
      destroyNewsEditor();
      // Reset form when closing to prepare for next "Add News"
      resetFormForCreate();
    });
    // If form is already visible (showCreateForm=true), init immediately
    if (newsCreateForm.classList.contains('show')) {
      initFormWidget();
    }
  }

  // Handle "Add News" button click - reset form before opening
  const addNewsButton = document.querySelector('[data-bs-target="#newsCreateForm"]');
  if (addNewsButton && !addNewsButton.classList.contains('btn-outline-secondary')) {
    // Only the main "Add News" button (not the cancel button which also targets the collapse)
    addNewsButton.addEventListener('click', () => {
      resetFormForCreate();
    });
  }

  // ===================
  // INITIALIZE VIEW WIDGET (news list page)
  // ===================

  initLanguageWidget({
    widgetId: 'news-view',
    onLanguageChange: selectedLang => {
      // Update news list display
      document.querySelectorAll('.news-title-col').forEach(el => {
        const title =
          el.dataset[
            `title${selectedLang.charAt(0).toUpperCase() + selectedLang.slice(1)}`
          ] ||
          el.dataset.titleEn ||
          'No title';
        const textEl = el.querySelector('.news-title-text');
        if (textEl) textEl.textContent = title;
      });

      document.querySelectorAll('.news-content-col').forEach(el => {
        const content =
          el.dataset[
            `content${selectedLang.charAt(0).toUpperCase() + selectedLang.slice(1)}`
          ] ||
          el.dataset.contentEn ||
          '';
        const textEl = el.querySelector('.news-content-text');
        if (textEl) textEl.textContent = content;
      });
    },
  });

  // ===================
  // EVENT LISTENERS
  // ===================

  // Real-time update on input
  document.addEventListener('input', e => {
    // Ensure translations object exists for current language
    if (!translations[currentLang]) {
      translations[currentLang] = { title: '', content: '', link: '' };
    }

    if (e.target.id === 'news_title') {
      translations[currentLang].title = e.target.value;
      updateFormWidgetDisplay();
    }
    if (e.target.id === 'news_content') {
      translations[currentLang].content = e.target.value;
      updateFormWidgetDisplay();
    }
    if (e.target.id === 'news_link') {
      translations[currentLang].link = e.target.value;
    }
  });

  // Save before form submit
  document.addEventListener('submit', e => {
    if (e.target.closest('#news-form')) {
      // Check if content exceeds limit
      if (isOverLimit()) {
        e.preventDefault();
        const message = config.translations?.contentTooLong ||
          `Content exceeds the limit of ${NEWS_CONTENT_MAX_LENGTH} characters.`;
        alert(message);
        return;
      }
      saveCurrentLanguage();
      config.acceptedLanguages.forEach(lang => updateHiddenInputs(lang));
    }
  });

  // Modal support
  document.addEventListener('shown.bs.modal', () => {
    initFormWidget();
  });

  // Initialize translations immediately (for early input events)
  initTranslations();

  // ===================
  // EDIT BUTTON HANDLER
  // ===================
  document.querySelectorAll('.btn-edit-news').forEach(btn => {
    btn.addEventListener('click', async() => {
      const newsItem = btn.closest('.news-item');
      const editUrl = btn.dataset.editUrl;

      // Fill translations from data attributes
      config.acceptedLanguages.forEach(lang => {
        const titleKey = `title${lang.charAt(0).toUpperCase() + lang.slice(1)}`;
        const contentKey = `content${lang.charAt(0).toUpperCase() + lang.slice(1)}`;
        const linkKey = `link${lang.charAt(0).toUpperCase() + lang.slice(1)}`;

        translations[lang] = {
          title: newsItem.dataset[titleKey] || '',
          content: newsItem.dataset[contentKey] || '',
          link: newsItem.dataset[linkKey] || '',
        };

        // Update hidden inputs
        const titleHidden = document.getElementById(
          `translation-title-${lang}`
        );
        const contentHidden = document.getElementById(
          `translation-content-${lang}`
        );
        const linkHidden = document.getElementById(`translation-link-${lang}`);

        if (titleHidden) titleHidden.value = translations[lang].title;
        if (contentHidden) contentHidden.value = translations[lang].content;
        if (linkHidden) linkHidden.value = translations[lang].link;
      });

      // Load default language
      loadLanguage(config.defaultLanguage);

      // Update status
      const statusSelect = document.getElementById('news_status');
      if (statusSelect) statusSelect.value = newsItem.dataset.visibility;

      // Change form action
      const form = document.getElementById('news-form');
      if (form) form.action = editUrl;

      // Change CSRF token
      const tokenInput = form?.querySelector('input[name="_token"]');
      const editToken = document.getElementById('csrf-token-edit');
      if (tokenInput && editToken) tokenInput.value = editToken.value;

      // Initialize widget and editor
      initFormWidget();
      await initNewsEditor();

      // Load content into editor
      if (editorInitialized) {
        setEditorContent(translations[config.defaultLanguage]?.content || '');
      }
      updateFormWidgetDisplay();

      // Open collapse
      const collapseEl = document.getElementById('newsCreateForm');
      if (collapseEl) {
        const bsCollapse = new Collapse(collapseEl, {
          toggle: false,
        });
        bsCollapse.show();
      }
    });
  });

  // ===================
  // DELETE BUTTON HANDLER
  // ===================

  const deleteModal = document.getElementById('deleteNewsModal');
  const deleteForm = document.getElementById('delete-news-form');
  const deleteNewsTitle = document.getElementById('delete-news-title');

  if (deleteModal && deleteForm) {
    const bsDeleteModal = new Modal(deleteModal);

    document.querySelectorAll('.btn-delete-news').forEach(btn => {
      btn.addEventListener('click', async() => {
        const newsId = btn.dataset.newsId;
        const deleteUrl = btn.dataset.deleteUrl;
        const newsTitle = btn.dataset.newsTitle;

        // Set form action
        deleteForm.action = deleteUrl;

        // Display news title in modal
        if (deleteNewsTitle) {
          deleteNewsTitle.textContent = newsTitle;
        }

        // Show modal
        bsDeleteModal.show();
      });
    });
  }
});
