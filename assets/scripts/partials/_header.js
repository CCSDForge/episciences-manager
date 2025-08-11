document.addEventListener('DOMContentLoaded', function () {
  // Function to perform language switch
  function performLanguageSwitch(selectedLocale) {
    let currentUrl = window.location.href;

    // Remove hash from URL for processing
    let hash = window.location.hash || '';
    let baseUrl = currentUrl.replace(hash, '');

    // Create URL object from base URL (without hash)
    const url = new URL(baseUrl);

    // Get current path segments
    const pathSegments = url.pathname
      .split('/')
      .filter(segment => segment !== '');

    // Check if first segment is a locale (en or fr)
    if (
      pathSegments.length > 0 &&
      (pathSegments[0] === 'en' || pathSegments[0] === 'fr')
    ) {
      // Replace the locale
      pathSegments[0] = selectedLocale;
    } else {
      // Add locale at the beginning
      pathSegments.unshift(selectedLocale);
    }

    // Reconstruct the URL with the new locale
    const newPath = '/' + pathSegments.join('/');
    const finalUrl = url.origin + newPath + url.search + hash;

    // Check if we're on a page route that supports AJAX loading
    if (
      pathSegments.length >= 5 &&
      pathSegments[1] === 'journal' &&
      pathSegments[3] === 'page'
    ) {
      // This is a page route, try to load content via AJAX
      loadPageContentAjax(finalUrl, selectedLocale, hash);
    } else {
      // Update translations before navigating for non-AJAX routes
      updateTranslations(selectedLocale);
      // For other routes, navigate normally
      window.location.href = finalUrl;
    }
  }

  // Function to load page content via AJAX
  function loadPageContentAjax(newUrl, selectedLocale, hash) {
    fetch(newUrl, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        // Update page title - try multiple selectors to ensure we catch it
        if (data.title && data.title[selectedLocale]) {
          // Try h1 element first
          const titleElement = document.querySelector('h1');
          if (titleElement) {
            titleElement.textContent = data.title[selectedLocale];
          }

          // Also try page-title id if it exists (from journalDetails.js)
          const pageTitle = document.getElementById('page-title');
          if (pageTitle) {
            pageTitle.textContent = data.title[selectedLocale];
          }
        }

        // Update page content - try multiple selectors
        if (data.content && data.content[selectedLocale]) {
          // Try .page-content class first
          const contentElement = document.querySelector('.page-content');
          if (contentElement) {
            contentElement.innerHTML = data.content[selectedLocale];
          }

          // Also try page-body id if it exists (from journalDetails.js)
          const pageBody = document.getElementById('page-body');
          if (pageBody) {
            pageBody.innerHTML = data.content[selectedLocale];
          }
        }

        // Update document title
        if (data.title && data.title[selectedLocale]) {
          document.title = data.title[selectedLocale];
        }

        // Update URL without page reload
        history.pushState({}, '', newUrl + hash);

        // Update page lang attribute
        document.documentElement.lang = selectedLocale;

        // Update language button text
        const languageToggle = document.getElementById(
          'language-dropdown-toggle',
        );
        if (languageToggle) {
          const iconElement = languageToggle.querySelector('i.fas.fa-globe');
          if (iconElement) {
            languageToggle.innerHTML =
              iconElement.outerHTML + ' ' + selectedLocale.toUpperCase();
          } else {
            languageToggle.innerHTML =
              '<i class="fas fa-globe me-1"></i> ' +
              selectedLocale.toUpperCase();
          }
        }

        // Update page navigation links to use the new locale
        updatePageNavLinks(selectedLocale);

        // Update translations and modal content
        updateTranslations(selectedLocale);
      })
      .catch(error => {
        console.error('Error loading page content:', error);
        // Fallback to normal page navigation
        window.location.href = newUrl + hash;
      });
  }

  // Custom dropdown behavior - intercept Bootstrap dropdown
  const dropdownToggle = document.getElementById('language-dropdown-toggle');
  const dropdownMenu = document.getElementById('language-dropdown-menu');

  if (dropdownToggle && dropdownMenu) {
    // Disable Bootstrap dropdown behavior and handle manually
    dropdownToggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Toggle dropdown menu visibility
      if (dropdownMenu.style.display === 'block') {
        dropdownMenu.style.display = 'none';
        dropdownToggle.setAttribute('aria-expanded', 'false');
        dropdownToggle.classList.remove('show');
      } else {
        dropdownMenu.style.display = 'block';
        dropdownMenu.style.position = 'absolute';
        dropdownMenu.style.top = '100%';
        dropdownMenu.style.left = '0';
        dropdownMenu.style.zIndex = '1000';
        dropdownToggle.setAttribute('aria-expanded', 'true');
        dropdownToggle.classList.add('show');
      }
    });

    // Handle clicks on language options
    const languageLinks = dropdownMenu.querySelectorAll('.language-switch');
    languageLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const selectedLocale = this.getAttribute('data-locale');

        if (selectedLocale) {
          // Hide dropdown
          dropdownMenu.style.display = 'none';
          dropdownToggle.setAttribute('aria-expanded', 'false');
          dropdownToggle.classList.remove('show');

          // Perform language switch
          performLanguageSwitch(selectedLocale);
        }
      });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
      if (
        !dropdownToggle.contains(e.target) &&
        !dropdownMenu.contains(e.target)
      ) {
        dropdownMenu.style.display = 'none';
        dropdownToggle.setAttribute('aria-expanded', 'false');
        dropdownToggle.classList.remove('show');
      }
    });
  }

  // Function to update translations and modal content
  function updateTranslations(newLocale) {
    console.log('updateTranslations called with locale:', newLocale);
    // Fetch new translations from the server
    fetch(`/${newLocale}/api/translations/${newLocale}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
      .then(response => {
        console.log('Translation response:', response.status);
        return response.json();
      })
      .then(translations => {
        console.log('New translations loaded:', translations);
        // Update window.translations object
        window.translations = translations;

        // Update modal content if the function exists
        if (typeof window.updateModalTranslations === 'function') {
          console.log('Calling updateModalTranslations function');
          window.updateModalTranslations();
        } else {
          console.log('updateModalTranslations function not found');
        }
      })
      .catch(error => {
        console.error('Error loading translations:', error);
      });
  }

  // Function to update page navigation links with new locale
  function updatePageNavLinks(newLocale) {
    const pageLinks = document.querySelectorAll('.page-nav-link');
    pageLinks.forEach(link => {
      const pageCode = link.getAttribute('data-page-code');
      const journalCode = link.getAttribute('data-journal-code');
      if (pageCode && journalCode) {
        // Update the href to use the new locale but keep it as # for AJAX handling
        // This ensures that if someone copies a link, it will have the correct locale
        const newUrl = `/${newLocale}/journal/${journalCode}/page/${pageCode}`;
        link.setAttribute('data-url', newUrl);

        // Fetch the page data to get the title in the new locale
        fetch(newUrl, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
        })
          .then(response => response.json())
          .then(data => {
            if (data.title && data.title[newLocale]) {
              link.textContent = data.title[newLocale];
            }
          })
          .catch(error => {
            console.error('Error updating page nav link:', error);
            // Keep original text if there's an error
          });
      }
    });
  }
});
