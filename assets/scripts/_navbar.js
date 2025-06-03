
    document.addEventListener('DOMContentLoaded', function() {
    // Get current path
    const path = window.location.pathname;

    // Remove trailing slash if it exists
    const cleanPath = path.endsWith('/') ? path.slice(0, -1) : path;

    // Handle root path (home page)
    if (cleanPath === '' || cleanPath === '/') {
    document.getElementById('nav-home').classList.add('active');
} else {
    // Handle other pages
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
    if (link.getAttribute('href') === cleanPath ||
    cleanPath.startsWith(link.getAttribute('href') + '/')) {
    link.classList.add('active');
         }
    });
    }
});
