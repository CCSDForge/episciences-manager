import { Controller } from '@hotwired/stimulus';
import * as bootstrap from 'bootstrap';

/**
 * Stimulus controller for hover card popover.
 * Displays a preview card on hover with database info.
 */
export default class extends Controller {
  static values = {
    logo: String,
    status: String,
    statusLabel: String,
    url: String,
    journalCount: Number,
    clickText: String,
  };

  connect() {
    this.popover = new bootstrap.Popover(this.element, {
      trigger: 'hover focus',
      html: true,
      placement: 'right',
      customClass: 'popover-card',
      content: this.buildContent(),
    });
  }

  disconnect() {
    this.popover?.dispose();
  }

  buildContent() {
    let html = '<div style="min-width: 250px;">';

    if (this.logoValue) {
      html += `<img src="${this.logoValue}" alt="" class="popover-logo mb-2">`;
    }

    html += `<div><strong>Status:</strong> <span class="badge bg-${this.statusColorClass}">${this.statusLabelValue}</span></div>`;

    if (this.urlValue) {
      html += `<div class="text-truncate" style="max-width: 230px;"><strong>URL:</strong> ${this.urlValue}</div>`;
    }

    html += `<div><strong>Journals:</strong> ${this.journalCountValue}</div>`;
    html += `<div class="text-muted mt-2"><small>${this.clickTextValue}</small></div>`;
    html += '</div>';

    return html;
  }

  get statusColorClass() {
    const colors = {
      pending: 'warning',
      validated: 'success',
      rejected: 'danger',
    };
    return colors[this.statusValue] || 'secondary';
  }
}
