document.addEventListener('DOMContentLoaded', function () {
  const saveButton = document.getElementById('saveSettings');
  const alertsContainer = document.getElementById('settings-alerts');

  /**
   * Escape HTML special characters to prevent XSS
   */
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  if (saveButton) {
    saveButton.addEventListener('click', function () {
      // Collect form data
      const data = {
        api_domain: document.getElementById('api_domain').value,
        theme: {
          primaryColor: document.getElementById('theme_primaryColor').value,
          primaryTextColor: document.getElementById('theme_primaryTextColor')
            .value,
        },
        languages: {
          accepted: [
            ...document.querySelectorAll('.language-checkbox:checked'),
          ].map(el => el.value),
          default: document.getElementById('languages_default').value,
        },
        homepage: collectHomepageData(),
        homepageRightBlock: collectHomepageRightBlockData(),
        menu: collectMenuData(),
        statistics: collectStatisticsData(),
      };

      // Disable button during request
      saveButton.disabled = true;
      saveButton.innerHTML =
        '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

      // Build URL with CSRF token
      const url = new URL(
        window.journalFrontendSettingsData.updateUrl,
        window.location.origin
      );
      url.searchParams.set(
        '_token',
        window.journalFrontendSettingsData.csrfToken
      );

      // Send request
      fetch(url.toString(), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      })
        .then(response => {
          return response.json().then(data => ({
            ok: response.ok,
            data: data,
          }));
        })
        .then(({ ok, data }) => {
          if (ok && data.success) {
            showAlert(
              'success',
              window.journalFrontendSettingsData.translations.saved
            );
          } else {
            let errorMessage = escapeHtml(
              data.message ||
                window.journalFrontendSettingsData.translations.error
            );
            if (data.errors) {
              const errorDetails = Object.values(data.errors)
                .map(escapeHtml)
                .join('<br>');
              errorMessage += '<br><small>' + errorDetails + '</small>';
            }
            showAlert('danger', errorMessage, true);
            console.error('Validation errors:', data);
          }
        })
        .catch(error => {
          showAlert(
            'danger',
            window.journalFrontendSettingsData.translations.error
          );
          console.error('Error:', error);
        })
        .finally(() => {
          // Re-enable button
          saveButton.disabled = false;
          saveButton.innerHTML =
            '<i class="fas fa-save me-2"></i>' +
            window.journalFrontendSettingsData.translations.save;
        });
    });
  }

  /**
   * Collect homepage options data (boolean checkboxes only)
   */
  function collectHomepageData() {
    const homepage = {};

    document.querySelectorAll('.homepage-option').forEach(checkbox => {
      const optionName = checkbox.id.replace('homepage_', '');
      homepage[optionName] = checkbox.checked;
    });

    return homepage;
  }

  /**
   * Collect homepage layout data (select options)
   */
  function collectHomepageRightBlockData() {
    return {
      lastInformationRenderType: document.getElementById(
        'homepageRightBlock_lastInformationRenderType'
      ).value,
    };
  }

  /**
   * Collect menu options data
   */
  function collectMenuData() {
    const menu = {};

    document.querySelectorAll('.menu-option').forEach(checkbox => {
      const optionName = checkbox.id.replace('menu_', '');
      menu[optionName] = checkbox.checked;
    });

    return menu;
  }

  /**
   * Collect statistics data (colors and options)
   */
  function collectStatisticsData() {
    const statistics = {
      colors: [],
    };

    // Collect colors
    document.querySelectorAll('.stats-color').forEach(colorInput => {
      statistics.colors.push(colorInput.value);
    });

    // Collect render and order options
    document.querySelectorAll('.stats-render').forEach(checkbox => {
      const optionName = checkbox.id
        .replace('stats_', '')
        .replace('_render', '');
      if (!statistics[optionName]) {
        statistics[optionName] = {};
      }
      statistics[optionName].render = checkbox.checked;
    });

    document.querySelectorAll('.stats-order').forEach(input => {
      const optionName = input.id.replace('stats_', '').replace('_order', '');
      if (!statistics[optionName]) {
        statistics[optionName] = {};
      }
      statistics[optionName].order = parseInt(input.value, 10);
    });

    return statistics;
  }

  /**
   * Show alert message
   * @param {string} type - Alert type (success, danger, warning, info)
   * @param {string} message - Message to display
   * @param {boolean} isHtmlSafe - If true, message is already escaped and can contain safe HTML like <br>
   */
  function showAlert(type, message, isHtmlSafe = false) {
    const validTypes = ['success', 'danger', 'warning', 'info'];
    const safeType = validTypes.includes(type) ? type : 'info';
    const safeMessage = isHtmlSafe ? message : escapeHtml(message);

    alertsContainer.innerHTML = `
            <div class="alert alert-${safeType} alert-dismissible fade show" role="alert">
                ${safeMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

    // Auto-hide after 5 seconds
    setTimeout(() => {
      const alert = alertsContainer.querySelector('.alert');
      if (alert) {
        alert.remove();
      }
    }, 5000);
  }

  // Sync color picker with text input
  document.querySelectorAll('input[type="color"]').forEach(colorPicker => {
    const textInputId = colorPicker.id.replace('_picker', '');
    const textInput = document.getElementById(textInputId);

    if (textInput) {
      colorPicker.addEventListener('input', () => {
        textInput.value = colorPicker.value;
      });

      textInput.addEventListener('input', () => {
        if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
          colorPicker.value = textInput.value;
        }
      });
    }
  });

  // Update theme preview on color change
  const primaryColorInput = document.getElementById('theme_primaryColor');
  const primaryTextColorInput = document.getElementById(
    'theme_primaryTextColor'
  );
  const themePreview = document.getElementById('theme-preview');
  const contrastRatioValue = document.getElementById('contrast-ratio-value');
  const contrastRatioLevel = document.getElementById('contrast-ratio-level');

  /**
   * Convert hex color to RGB
   */
  function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result
      ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16),
        }
      : null;
  }

  /**
   * Calculate relative luminance (WCAG formula)
   */
  function getLuminance(r, g, b) {
    const [rs, gs, bs] = [r, g, b].map(c => {
      c = c / 255;
      return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
  }

  /**
   * Calculate contrast ratio between two colors
   */
  function getContrastRatio(color1, color2) {
    const rgb1 = hexToRgb(color1);
    const rgb2 = hexToRgb(color2);
    if (!rgb1 || !rgb2) return null;

    const l1 = getLuminance(rgb1.r, rgb1.g, rgb1.b);
    const l2 = getLuminance(rgb2.r, rgb2.g, rgb2.b);

    const lighter = Math.max(l1, l2);
    const darker = Math.min(l1, l2);

    return (lighter + 0.05) / (darker + 0.05);
  }

  /**
   * Update contrast ratio display
   */
  function updateContrastRatio() {
    if (!contrastRatioValue || !contrastRatioLevel) return;

    const ratio = getContrastRatio(
      primaryColorInput.value,
      primaryTextColorInput.value
    );
    if (ratio === null) {
      contrastRatioValue.textContent = '--';
      contrastRatioLevel.textContent = '--';
      return;
    }

    contrastRatioValue.textContent = ratio.toFixed(2) + ':1';

    if (ratio >= 7) {
      contrastRatioLevel.textContent = 'AAA';
      contrastRatioLevel.className = 'badge bg-success mt-2';
    } else if (ratio >= 4.5) {
      contrastRatioLevel.textContent = 'AA';
      contrastRatioLevel.className = 'badge bg-warning text-dark mt-2';
    } else {
      contrastRatioLevel.textContent =
        contrastRatioLevel.dataset.failLabel || 'Fail';
      contrastRatioLevel.className = 'badge bg-danger mt-2';
    }
  }

  if (primaryColorInput && primaryTextColorInput && themePreview) {
    const updatePreview = () => {
      themePreview.style.backgroundColor = primaryColorInput.value;
      themePreview.style.color = primaryTextColorInput.value;
      updateContrastRatio();
    };

    primaryColorInput.addEventListener('input', updatePreview);
    primaryTextColorInput.addEventListener('input', updatePreview);
    document
      .getElementById('theme_primaryColor_picker')
      ?.addEventListener('input', updatePreview);
    document
      .getElementById('theme_primaryTextColor_picker')
      ?.addEventListener('input', updatePreview);

    // Initial calculation
    updateContrastRatio();
  }

  // Update chart preview on color change
  const statsColorInputs = document.querySelectorAll('.stats-color');
  const chartPreview = document.getElementById('chart-preview');

  function updateChartPreview() {
    const colors = [...statsColorInputs].map(input => input.value);
    if (chartPreview && colors.length >= 4) {
      chartPreview.style.background = `conic-gradient(
                ${colors[0]} 0deg 90deg,
                ${colors[1]} 90deg 126deg,
                ${colors[2]} 126deg 198deg,
                ${colors[3]} 198deg 360deg
            )`;
    }
  }

  statsColorInputs.forEach((colorInput, index) => {
    colorInput.addEventListener('input', () => {
      // Update chart
      updateChartPreview();
      // Update legend color
      const legendColor = document.getElementById(`legend-color-${index}`);
      if (legendColor) {
        legendColor.style.background = colorInput.value;
      }
    });
  });
});
