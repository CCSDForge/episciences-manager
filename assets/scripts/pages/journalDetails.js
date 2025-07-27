
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const pageLinks = document.querySelectorAll('.page-nav-link');
    const pageContent = document.getElementById('page-content');
    const pageBody = document.getElementById('page-body');

    console.log('Found links:', pageLinks.length);
    console.log('Page content element:', pageContent);

    pageLinks.forEach(link => {
    link.addEventListener('click', function(e) {
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

    // Remove active class from all links
    pageLinks.forEach(l => l.classList.remove('active'));
    // Add active class to clicked link
    this.classList.add('active');
    // Extract the locale from the URL, or use the document's locale if it has been changed
    let locale = document.documentElement.lang || window.location.pathname.split('/')[1] || 'en';
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
    'X-Requested-With': 'XMLHttpRequest'
}
})
    .then(response => {
    console.log('Response status:', response.status);
    return response.json();
})
    .then(data => {
    console.log('Data received:', data);
    if (data.error) {
    pageBody.innerHTML = '<p class="text-danger">Page non trouvée</p>';
} else {
    // Use the locale extracted from the URL
    const currentLocale = locale;
    //the content is HTML converted from markdown, so it's safe to use innerHTML
    pageBody.innerHTML = data.content[currentLocale] || data.content['en'] || 'Pas de contenu disponible';
}
    pageContent.style.display = 'block';
})
    .catch(error => {
    console.error('Fetch error:', error);
    pageBody.innerHTML = '<p class="text-danger">Erreur lors du chargement du contenu</p>';
    pageContent.style.display = 'block';
            });
        });
    });
});
