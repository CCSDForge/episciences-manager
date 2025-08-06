import 'bootstrap';


document.addEventListener('DOMContentLoaded', function () {
    const logoutAlert = document.querySelector('.alert-success');

    if (logoutAlert && logoutAlert.textContent.includes('Déconnexion réussie')) {
        // Attendre 5 secondes avant de fermer l'alerte
        setTimeout(() => {
            try {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(logoutAlert);
                bsAlert.close();
            } catch (e) {
                console.error('Erreur lors de la fermeture de l’alerte Bootstrap :', e);
            }
        }, 5000);
    }
});
