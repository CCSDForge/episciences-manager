import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for custom file input.
 * Displays the selected filename with translated placeholder text.
 */
export default class extends Controller {
  static targets = ['input', 'filename'];

  static values = {
    placeholder: String,
  };

  /**
   * Update the displayed filename when a file is selected
   */
  updateFilename() {
    const file = this.inputTarget.files[0];
    this.filenameTarget.textContent = file?.name || this.placeholderValue;
  }
}
