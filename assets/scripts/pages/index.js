import { Alert } from 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
  const logoutAlert = document.querySelector('.alert-success');

  if (logoutAlert && logoutAlert.textContent.includes('Déconnexion réussie')) {
    // Wait 5 seconds before closing the alert
    setTimeout(() => {
      try {
        const bsAlert = Alert.getOrCreateInstance(logoutAlert);
        bsAlert.close();
      } catch (e) {
        console.error('Error while closing the Bootstrap alert:', e);
      }
    }, 5000);
  }
});
