import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for backoffice settings form.
 * Provides UX enhancements (loading state) while Symfony handles form submission.
 */
export default class extends Controller {
  static targets = ['saveButton'];

  /**
   * Handle form submission - add loading state to button
   * The form submits normally (Symfony handles POST)
   */
  onSubmit() {
    if (!this.hasSaveButtonTarget) return;

    // Show loading state
    this.saveButtonTarget.disabled = true;
    this.saveButtonTarget.innerHTML =
      '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
  }
}
