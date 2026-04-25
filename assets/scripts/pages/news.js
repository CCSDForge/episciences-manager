import { initLanguageWidget } from '../components/language-widget.js';

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
      content: getContentInput()?.value || '',
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

  // Initialize when collapse is shown
  const newsCreateForm = getElement('newsCreateForm');
  if (newsCreateForm) {
    newsCreateForm.addEventListener('shown.bs.collapse', () => {
      initFormWidget();
    });

    // If form is already visible (showCreateForm=true), init immediately
    if (newsCreateForm.classList.contains('show')) {
      initFormWidget();
    }
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
    if (e.target.closest('form')) {
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
});
