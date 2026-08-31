import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for journal indexing database management.
 * Handles toggle switches with AJAX.
 */
export default class extends Controller {
  static targets = ['toggle'];

  static values = {
    associatedMessage: String,
    dissociatedMessage: String,
  };

  /**
   * Toggle association between journal and indexing database
   */
  async toggle(event) {
    const checkbox = event.target;
    const url = checkbox.dataset.url;
    const token = checkbox.dataset.token;
    const name = checkbox.dataset.name;

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: '_token=' + encodeURIComponent(token),
      });

      const data = await response.json();

      if (data.success) {
        const message =
          data.action === 'associated'
            ? this.associatedMessageValue.replace('%name%', name)
            : this.dissociatedMessageValue.replace('%name%', name);
        this.showAlert('success', message);
      } else {
        checkbox.checked = !checkbox.checked; // Revert
        this.showAlert('danger', data.error || 'An error occurred');
      }
    } catch (error) {
      checkbox.checked = !checkbox.checked; // Revert
      this.showAlert('danger', 'Error: ' + error.message);
    }
  }

  /**
   * Display a toast-style alert message (bottom-right)
   * Uses the existing #flash-messages-container from flash_messages.html.twig
   */
  showAlert(type, message) {
    // Map type to Bootstrap class and icon
    const config = {
      success: { class: 'alert-success', icon: 'fa-check-circle' },
      danger: { class: 'alert-danger', icon: 'fa-exclamation-circle' },
      warning: { class: 'alert-warning', icon: 'fa-exclamation-triangle' },
      info: { class: 'alert-info', icon: 'fa-info-circle' },
    };
    const { class: alertClass, icon } = config[type] || config.info;

    const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

    // Use the existing flash messages container
    const container = document.getElementById('flash-messages-container');
    if (container) {
      container.insertAdjacentHTML('beforeend', alertHtml);

      // Auto-dismiss after 5 seconds
      const alertElement = container.lastElementChild;
      setTimeout(() => alertElement?.remove(), 5000);
    }
  }
}
