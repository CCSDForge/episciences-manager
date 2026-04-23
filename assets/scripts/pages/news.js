document.addEventListener('DOMContentLoaded', function () {
  // ===================
  // DYNAMIC ELEMENT GETTERS
  // ===================

  // Get elements dynamically (for modal support)
  const getElement = (id) => document.getElementById(id);
  const getLanguageSelect = () => getElement('content-language-select');
  const getTitleInput = () => getElement('news_title');
  const getContentInput = () => getElement('news_content');
  const getLinkInput = () => getElement('news_link');
  const getTitleBadge = () => getElement('title-lang-badge');
  const getContentBadge = () => getElement('content-lang-badge');
  const getTranslationsList = () => getElement('translations-list');
  const getFormLanguageInput = () => getElement('form-language');

  // ===================
  // CONFIGURATION
  // ===================

  // Configuration from Twig (set in _language_widget.html.twig)
  const config = window.newsLanguageConfig || {
    acceptedLanguages: ['en', 'fr'],
    defaultLanguage: 'en',
    langNames: {},
    mode: 'create',
  };

  // ===================
  // TRANSLATIONS STORAGE
  // ===================

  // Store translations per language
  const translations = {};

  // Current active language
  let currentLang = config.defaultLanguage;

  // Initialize translations from hidden inputs or empty
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

    // Set current language from dropdown or default
    const dropdown = getLanguageSelect();
    currentLang = dropdown?.value || config.defaultLanguage;
  }

  // ===================
  // CORE FUNCTIONS
  // ===================

  /**
   * Save current language data to translations object and hidden inputs
   */
  function saveCurrentLanguage() {
    if (!currentLang) return;

    const titleInput = getTitleInput();
    const linkInput = getLinkInput();

    translations[currentLang] = {
      title: titleInput?.value || '',
      content: getContentValue(),
      link: linkInput?.value || '',
    };

    // Update hidden inputs
    updateHiddenInputs(currentLang);

    // Update row display
    updateTranslationRow(currentLang);

    // Update dropdown options
    updateDropdown();
  }

  /**
   * Load language data into form fields
   * @param {string} lang - Language code
   */
  function loadLanguage(lang) {
    currentLang = lang;

    const data = translations[lang] || { title: '', content: '', link: '' };

    const titleInput = getTitleInput();
    const linkInput = getLinkInput();
    const formLanguageInput = getFormLanguageInput();
    const titleBadge = getTitleBadge();
    const contentBadge = getContentBadge();

    if (titleInput) titleInput.value = data.title;
    setContentValue(data.content);
    if (linkInput) linkInput.value = data.link;

    // Update hidden field and badges
    if (formLanguageInput) formLanguageInput.value = lang;
    if (titleBadge) titleBadge.textContent = lang.toUpperCase();
    if (contentBadge) contentBadge.textContent = lang.toUpperCase();
  }

  /**
   * Get content value (compatible with TinyMCE)
   * @returns {string}
   */
  function getContentValue() {
    if (typeof tinymce !== 'undefined' && tinymce.get('news_content')) {
      return tinymce.get('news_content').getContent();
    }
    const contentInput = getContentInput();
    return contentInput?.value || '';
  }

  /**
   * Set content value (compatible with TinyMCE)
   * @param {string} value
   */
  function setContentValue(value) {
    if (typeof tinymce !== 'undefined' && tinymce.get('news_content')) {
      tinymce.get('news_content').setContent(value || '');
    } else {
      const contentInput = getContentInput();
      if (contentInput) {
        contentInput.value = value || '';
      }
    }
  }

  /**
   * Update hidden inputs for a specific language
   * @param {string} lang - Language code
   */
  function updateHiddenInputs(lang) {
    const titleHidden = getElement(`translation-title-${lang}`);
    const contentHidden = getElement(`translation-content-${lang}`);
    const linkHidden = getElement(`translation-link-${lang}`);

    if (titleHidden) titleHidden.value = translations[lang]?.title || '';
    if (contentHidden) contentHidden.value = translations[lang]?.content || '';
    if (linkHidden) linkHidden.value = translations[lang]?.link || '';
  }

  /**
   * Update translation row display (icon and preview)
   * @param {string} lang - Language code
   */
  function updateTranslationRow(lang) {
    const translationsList = getTranslationsList();
    const row = translationsList?.querySelector(`[data-lang="${lang}"]`);
    if (!row) return;

    const hasContent = !!(translations[lang]?.title || translations[lang]?.content);

    // Update data attribute
    row.dataset.hasContent = hasContent ? 'true' : 'false';

    // Update icon (+ or pencil)
    const icon = row.querySelector('.translation-action-btn i');
    if (icon) {
      icon.className = hasContent
        ? 'fas fa-pen text-primary'
        : 'fas fa-plus text-muted';
    }

    // Update title preview
    const titlePreview = row.querySelector('.translation-title-preview');
    if (titlePreview) {
      const title = translations[lang]?.title || '';
      if (title) {
        titlePreview.textContent = title.substring(0, 20) + (title.length > 20 ? '...' : '');
        titlePreview.title = title;
        titlePreview.classList.remove('text-muted', 'fst-italic');
      } else {
        titlePreview.textContent = '-';
        titlePreview.title = '';
        titlePreview.classList.add('text-muted', 'fst-italic');
      }
    }

    // Update content preview
    const contentPreview = row.querySelector('.translation-content-preview');
    if (contentPreview) {
      // Strip HTML tags for preview
      const content = (translations[lang]?.content || '').replace(/<[^>]*>/g, '');
      if (content) {
        contentPreview.textContent = content.substring(0, 30) + (content.length > 30 ? '...' : '');
        contentPreview.title = content;
        contentPreview.classList.remove('fst-italic');
      } else {
        contentPreview.innerHTML = '<span class="fst-italic">No content</span>';
        contentPreview.title = '';
      }
    }
  }

  /**
   * Update dropdown to show only languages with content + current language
   */
  function updateDropdown() {
    const languageSelect = getLanguageSelect();
    if (!languageSelect) return;

    const currentValue = currentLang;
    languageSelect.innerHTML = '';

    config.acceptedLanguages.forEach(lang => {
      const hasContent = !!(translations[lang]?.title || translations[lang]?.content);

      // Show if: has content OR is current language
      if (hasContent || lang === currentValue) {
        const option = document.createElement('option');
        option.value = lang;
        option.textContent = config.langNames[lang] || lang.toUpperCase();
        option.selected = lang === currentValue;
        languageSelect.appendChild(option);
      }
    });
  }

  /**
   * Update all translation rows
   */
  function updateAllRows() {
    config.acceptedLanguages.forEach(lang => {
      updateTranslationRow(lang);
    });
  }

  // ===================
  // EVENT LISTENERS (using event delegation)
  // ===================

  // Language change via dropdown
  document.addEventListener('change', function (e) {
    if (e.target.id === 'content-language-select') {
      saveCurrentLanguage();
      loadLanguage(e.target.value);
    }
  });

  // Click on + or pencil in translations list
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.translation-action-btn');
    if (!btn) return;

    const lang = btn.dataset.lang;
    if (lang && lang !== currentLang) {
      saveCurrentLanguage();
      loadLanguage(lang);
      updateDropdown();

      // Select in dropdown
      const dropdown = getLanguageSelect();
      if (dropdown) {
        dropdown.value = lang;
      }
    }
  });

  // Real-time update - Title (event delegation)
  document.addEventListener('input', function (e) {
    if (e.target.id === 'news_title') {
      translations[currentLang].title = e.target.value;
      updateTranslationRow(currentLang);
      updateDropdown();
    }

    if (e.target.id === 'news_content') {
      translations[currentLang].content = e.target.value;
      updateTranslationRow(currentLang);
      updateDropdown();
    }

    if (e.target.id === 'news_link') {
      translations[currentLang].link = e.target.value;
    }
  });

  // Real-time update - TinyMCE
  if (typeof tinymce !== 'undefined') {
    tinymce.on('AddEditor', function (e) {
      e.editor.on('input change keyup', function () {
        translations[currentLang].content = e.editor.getContent();
        updateTranslationRow(currentLang);
        updateDropdown();
      });
    });
  }

  // Save before form submit
  document.addEventListener('submit', function (e) {
    if (e.target.closest('form')) {
      saveCurrentLanguage();

      // Ensure all hidden inputs are up to date
      config.acceptedLanguages.forEach(lang => {
        updateHiddenInputs(lang);
      });
    }
  });

  // ===================
  // MODAL SUPPORT
  // ===================

  // Reinitialize when modal opens
  document.addEventListener('shown.bs.modal', function (e) {
    initTranslations();
    updateAllRows();
    updateDropdown();
    loadLanguage(currentLang);
  });

  // ===================
  // INITIALIZATION
  // ===================

  initTranslations();
  updateAllRows();
  updateDropdown();

  // ===================
  // VIEW MODE (news list page)
  // ===================

  const viewWidget = document.querySelector('[data-widget-mode="view"]');
  if (viewWidget) {
    document.addEventListener('change', function (e) {
      if (e.target.id !== 'content-language-select') return;
      if (!viewWidget) return;

      const selectedLang = e.target.value;

      document.querySelectorAll('.news-title-col').forEach(function (el) {
        const langKey = 'title' + selectedLang.charAt(0).toUpperCase() + selectedLang.slice(1);
        const title = el.dataset[langKey] || el.dataset.titleEn || 'No title';
        const textEl = el.querySelector('.news-title-text');
        if (textEl) textEl.textContent = title;
      });

      document.querySelectorAll('.news-content-col').forEach(function (el) {
        const langKey = 'content' + selectedLang.charAt(0).toUpperCase() + selectedLang.slice(1);
        const content = el.dataset[langKey] || el.dataset.contentEn || '';
        const textEl = el.querySelector('.news-content-text');
        if (textEl) textEl.textContent = content;
      });
    });
  }
});