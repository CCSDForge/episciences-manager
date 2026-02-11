document.addEventListener('DOMContentLoaded', function() {
    const saveButton = document.getElementById('saveConfiguration');
    const alertsContainer = document.getElementById('configuration-alerts');

    saveButton.addEventListener('click', function() {
        // Collect form data
        const data = {
            api_domain: document.getElementById('api_domain').value,
            theme: {
                primaryColor: document.getElementById('theme_primaryColor').value,
                primaryTextColor: document.getElementById('theme_primaryTextColor').value
            },
            languages: {
                accepted: [...document.querySelectorAll('.language-checkbox:checked')].map(el => el.value),
                default: document.getElementById('languages_default').value
            },
            homepage: collectHomepageData(),
            menu: collectMenuData(),
            statistics: collectStatisticsData()
        };

        // Disable button during request
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

        // Send request
        fetch(window.journalConfigurationData.updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    showAlert('success', window.journalConfigurationData.translations.saved);
                } else {
                    showAlert('danger', result.message || window.journalConfigurationData.translations.error);
                }
            })
            .catch(error => {
                showAlert('danger', window.journalConfigurationData.translations.error);
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable button
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fas fa-save me-2"></i>' + window.journalConfigurationData.translations.save;
            });
    });

    /**
     * Collect homepage options data
     */
    function collectHomepageData() {
        const homepage = {
            lastInformationRenderType: document.getElementById('homepage_lastInformationRenderType').value
        };

        document.querySelectorAll('.homepage-option').forEach(checkbox => {
            const optionName = checkbox.id.replace('homepage_', '');
            homepage[optionName] = checkbox.checked;
        });

        return homepage;
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
            colors: []
        };

        // Collect colors
        document.querySelectorAll('.stats-color').forEach(colorInput => {
            statistics.colors.push(colorInput.value);
        });

        // Collect render and order options
        document.querySelectorAll('.stats-render').forEach(checkbox => {
            const optionName = checkbox.id.replace('stats_', '').replace('_render', '');
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
     */
    function showAlert(type, message) {
        alertsContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
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
    const primaryTextColorInput = document.getElementById('theme_primaryTextColor');
    const themePreview = document.getElementById('theme-preview');

    if (primaryColorInput && primaryTextColorInput && themePreview) {
        const updatePreview = () => {
            themePreview.style.backgroundColor = primaryColorInput.value;
            themePreview.style.color = primaryTextColorInput.value;
        };

        primaryColorInput.addEventListener('input', updatePreview);
        primaryTextColorInput.addEventListener('input', updatePreview);
        document.getElementById('theme_primaryColor_picker')?.addEventListener('input', updatePreview);
        document.getElementById('theme_primaryTextColor_picker')?.addEventListener('input', updatePreview);
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