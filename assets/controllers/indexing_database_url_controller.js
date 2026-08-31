import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['error'];
  static values = { checkUrlPath: String };

  async validateUrl(event) {
    const url = event.target.value.trim();
    this.clearError();

    if (!url) return;

    try {
      const response = await fetch(this.checkUrlPathValue + '?url=' +
        encodeURIComponent(url));
      const data = await response.json();

      if (data.exists) {
        this.showError(event.target.dataset.errorMessage || 'URL already exists');
      }
    } catch (error) {
      // Silently fail
    }
  }

  showError(message) {
    this.errorTarget.textContent = message;
    this.errorTarget.classList.remove('d-none');
  }

  clearError() {
    this.errorTarget.textContent = '';
    this.errorTarget.classList.add('d-none');
  }
}
