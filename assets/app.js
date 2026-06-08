import './bootstrap.js';
import * as bootstrap from 'bootstrap';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

console.log('Welcome to Webpack Encore with Bootstrap!🎉');

/**
 * Auto-close alerts with data-auto-close attribute
 * Used for Symfony flash messages
 *
 * Alert systems in the application:
 * ---------------------------------
 * 1. Ajax alerts (JS-based, via showAlert functions):
 *    - journalPages.js -> #pages-alerts
 *    - journalFrontendSettings.js -> #settings-alerts
 *
 * 2. Symfony flash messages (server-side, via flash_messages.html.twig):
 *    - home/index.html.twig (logout success)
 *    - news/journalNews.html.twig (create, update, delete news)
 */
document.addEventListener('DOMContentLoaded', () => {
  const alertsWithAutoClose = document.querySelectorAll('[data-auto-close]');

  alertsWithAutoClose.forEach(alert => {
    const delay = parseInt(alert.dataset.autoClose, 10) || 5000;

    setTimeout(() => {
      // Use Bootstrap's alert close if available
      if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
      } else {
        alert.remove();
      }
    }, delay);
  });
});
