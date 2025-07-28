
import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const pageLinks = document.querySelectorAll('.page-nav-link');
    const pageContent = document.getElementById('page-content');
    const pageBody = document.getElementById('page-body');
    const editButton = document.getElementById('edit-button');
    const saveButton = document.getElementById('save-button');
    const pageTitleEdit = document.getElementById('page-title-edit');
    const pageContentEdit = document.getElementById('page-content-edit');
    
    let currentPageCode = null;
    let currentJournalCode = null;

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
    
    // Store current page info for editing
    currentPageCode = pageCode;
    currentJournalCode = journalCode;

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
    
    // Edit button handler
    editButton.addEventListener('click', function() {
        console.log('Edit button clicked');
        
        if (!currentPageCode || !currentJournalCode) {
            alert('Veuillez d\'abord sélectionner une page à éditer');
            return;
        }
        
        // Get current page title from active link
        const activeLink = document.querySelector('.page-nav-link.active');
        const pageTitle = activeLink ? activeLink.textContent.trim() : currentPageCode;
        
        // Get current content (strip HTML for editing)
        const currentContent = pageBody.innerHTML || '';
        
        // Populate modal
        pageTitleEdit.value = pageTitle;
        pageContentEdit.value = currentContent.replace(/<[^>]*>/g, ''); // Strip HTML tags
        
        console.log('Modal populated with:', { pageTitle, currentPageCode });
    });
    
    // Save button handler
    saveButton.addEventListener('click', function() {
        console.log('Save button clicked');
        
        if (!currentPageCode || !currentJournalCode) {
            alert('Erreur: informations de page manquantes');
            return;
        }
        
        const newContent = pageContentEdit.value;
        let locale = document.documentElement.lang || window.location.pathname.split('/')[1] || 'en';
        
        // Validate locale
        if (locale !== 'en' && locale !== 'fr') {
            locale = 'en';
        }
        
        const saveUrl = `/${locale}/journal/${currentJournalCode}/page/${currentPageCode}/edit`;
        
        console.log('Saving to:', saveUrl);
        console.log('Content:', newContent);
        
        // Save via AJAX
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                content: newContent,
                locale: locale
            })
        })
        .then(response => {
            console.log('Save response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Save response data:', data);
            if (data.success) {
                // Update page content with HTML converted content
                pageBody.innerHTML = data.htmlContent || newContent;
                
                // Close modal
                const modalElement = document.getElementById('editModal');
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                } else {
                    // If no instance exists, create one and hide it
                    new Modal(modalElement).hide();
                }
                
                // Show success message
                alert('Contenu sauvegardé avec succès!');
            } else {
                alert('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            console.error('Error stack:', error.stack);
            alert('Erreur lors de la sauvegarde: ' + error.message);
        });
    });
});
