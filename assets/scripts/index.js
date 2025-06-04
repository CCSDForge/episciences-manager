
    // Auto-dismiss logout message after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
    const logoutAlert = document.querySelector('.alert-success');
    if (logoutAlert && logoutAlert.textContent.includes('Déconnexion réussie')) {
    setTimeout(function() {
    const bsAlert = new bootstrap.Alert(logoutAlert);
    bsAlert.close();
}, 5000);
}
});