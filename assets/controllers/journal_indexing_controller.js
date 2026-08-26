import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for journal indexing database management.
 * Handles toggle switches with AJAX.
 */
export default class extends Controller {
    static targets = ['alert', 'toggle'];

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
                const message = data.action === 'associated'
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
     * Display an alert message
     */
    showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        this.alertTarget.insertAdjacentHTML('beforeend', alertHtml);

        // Auto-dismiss after 5 seconds
        const alertElement = this.alertTarget.lastElementChild;
        setTimeout(() => alertElement?.remove(), 5000);
    }
}
