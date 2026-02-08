document.addEventListener('DOMContentLoaded', function () {
  const searchForm = document.querySelector('.search-form');
  const searchInput = document.querySelector('input[name="search"]');
  const searchButton = document.querySelector('.search-btn');

  if (searchForm && searchInput && searchButton) {
    const originalIcon = searchButton.innerHTML;
    const clearIcon = '<i class="fas fa-times"></i>';

    // Get translated text from window.journalConfigData (passed from Symfony)
    const clearSearchText =
      window.journalConfigData?.translations?.clearSearch || 'Clear search';
    const searchText =
      window.journalConfigData?.translations?.searchPlaceholder || 'Search';

    // Function to update button state
    function updateSearchButton(isSearchActive) {
      if (isSearchActive) {
        searchButton.innerHTML = clearIcon;
        searchButton.title = clearSearchText;
        searchButton.setAttribute('type', 'button');
        searchButton.classList.add('clear-mode');
      } else {
        searchButton.innerHTML = originalIcon;
        searchButton.title = searchText;
        searchButton.setAttribute('type', 'submit');
        searchButton.classList.remove('clear-mode');
      }
    }

    // Check if search is currently active
    const hasActiveSearch =
      searchInput.value && window.location.search.includes('search=');
    updateSearchButton(hasActiveSearch);

    // Handle button click
    searchButton.addEventListener('click', function (e) {
      if (searchButton.classList.contains('clear-mode')) {
        e.preventDefault();
        searchInput.value = '';
        // Remove search parameter and redirect to clean URL
        const url = new URL(window.location);
        url.searchParams.delete('search');
        url.searchParams.delete('page');

        // Extract locale from pathname
        const pathSegments = window.location.pathname
          .split('/')
          .filter(segment => segment.length > 0);
        const locale = pathSegments[0];

        // Rebuild URL with locale
        if (locale === 'en' || locale === 'fr') {
          url.pathname = `/${locale}/journal/configuration`;
          url.search = '';
        } else {
          url.search = '';
        }

        window.location.href = url.toString();
      }
    });

    // Update button when input changes
    searchInput.addEventListener('input', function () {
      const hasValue = this.value.trim().length > 0;
      const isCurrentlySearching = window.location.search.includes('search=');
      updateSearchButton(hasValue && isCurrentlySearching);
    });
  }
});