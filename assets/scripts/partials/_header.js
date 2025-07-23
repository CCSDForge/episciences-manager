
    document.addEventListener('DOMContentLoaded', function() {
    // Function to perform language switch
    function performLanguageSwitch(selectedLocale) {
        let currentUrl = window.location.href;

        // Remove hash from URL for processing
        let hash = window.location.hash || '';
        let baseUrl = currentUrl.replace(hash, '');

        // Create URL object from base URL (without hash)
        const url = new URL(baseUrl);

        // Get current path segments
        const pathSegments = url.pathname.split('/').filter(segment => segment !== '');

        // Check if first segment is a locale (en or fr)
        if (pathSegments.length > 0 && (pathSegments[0] === 'en' || pathSegments[0] === 'fr')) {
            // Replace the locale
            pathSegments[0] = selectedLocale;
        } else {
            // Add locale at the beginning
            pathSegments.unshift(selectedLocale);
        }

        // Reconstruct the URL with the new locale
        const newPath = '/' + pathSegments.join('/');
        const finalUrl = url.origin + newPath + url.search + hash;

        // Navigate to the new URL
        window.location.href = finalUrl;
    }

    // Custom dropdown behavior - intercept Bootstrap dropdown
    const dropdownToggle = document.getElementById('language-dropdown-toggle');
    const dropdownMenu = document.getElementById('language-dropdown-menu');

    if (dropdownToggle && dropdownMenu) {
    // Disable Bootstrap dropdown behavior and handle manually
    dropdownToggle.addEventListener('click', function(e) {
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
    languageLinks.forEach(function(link) {
    link.addEventListener('click', function(e) {
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
    document.addEventListener('click', function(e) {
    if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
    dropdownMenu.style.display = 'none';
    dropdownToggle.setAttribute('aria-expanded', 'false');
    dropdownToggle.classList.remove('show');
}
});
}
});
