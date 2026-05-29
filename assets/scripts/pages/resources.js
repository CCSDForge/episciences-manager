import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
  console.log('Resources script loaded, looking for upload form...');
  console.log('DOM content loaded, document ready state:', document.readyState);

  const uploadForm = document.getElementById('uploadForm');
  console.log('Upload form element:', uploadForm);

  const fileInput = document.getElementById('fileInput');
  const fileInputDisplay = document.getElementById('fileInputDisplay');
  const fileInputBtn = document.getElementById('fileInputBtn');
  console.log('File input element:', fileInput);

  const uploadProgress = document.getElementById('uploadProgress');
  const uploadMessages = document.getElementById('uploadMessages');

  console.log('Looking for deleteConfirmModal...');
  const modalElement = document.getElementById('deleteConfirmModal');
  console.log('Modal element found:', modalElement);

  let deleteConfirmModal = null;
  let imageInsertModal = null;
  let linkInsertModal = null;
  let fileConflictModal = null;

  try {
    console.log('Checking Bootstrap Modal availability:', typeof Modal);
    console.log('Modal element exists:', !!modalElement);

    if (modalElement && Modal) {
      deleteConfirmModal = new Modal(modalElement);
      console.log('Modal initialized successfully:', deleteConfirmModal);
    } else {
      console.error('Cannot initialize modal - missing requirements:', {
        modalElement: !!modalElement,
        Modal: !!Modal,
      });
    }

    // Initialize image insert modal
    const imageModalElement = document.getElementById('imageInsertModal');
    if (imageModalElement && Modal) {
      imageInsertModal = new Modal(imageModalElement);
      console.log('Image insert modal initialized successfully');
    }

    // Initialize link insert modal
    const linkModalElement = document.getElementById('linkInsertModal');
    if (linkModalElement && Modal) {
      linkInsertModal = new Modal(linkModalElement);
      console.log('Link insert modal initialized successfully');
    }

    // Initialize file conflict modal
    const conflictModalElement = document.getElementById('fileConflictModal');
    if (conflictModalElement && Modal) {
      fileConflictModal = new Modal(conflictModalElement);
      console.log('File conflict modal initialized successfully');
    }
  } catch (error) {
    console.error('Error initializing modal:', error);
  }

  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  const fileToDelete = document.getElementById('fileToDelete');
  const renameFileBtn = document.getElementById('renameFileBtn');

  let fileToDeleteName = null;

  // Custom file input handlers
  if (fileInputBtn && fileInput && fileInputDisplay) {
    // Handle click on browse button
    fileInputBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener('change', function () {
      const files = fileInput.files;
      if (files.length > 0) {
        fileInputDisplay.value = files[0].name;
      } else {
        fileInputDisplay.value = '';
      }
    });

    // Reset display when form is reset
    if (uploadForm) {
      uploadForm.addEventListener('reset', function () {
        setTimeout(() => {
          fileInputDisplay.value = '';
        }, 0);
      });
    }
  }

  // Check window.resourcesData availability
  console.log('Checking window.resourcesData:', window.resourcesData);

  // Upload form handler
  if (uploadForm) {
    console.log('Upload form found, adding event listener...');
    console.log('Upload form element:', uploadForm);
    console.log('Window resourcesData:', window.resourcesData);

    uploadForm.addEventListener('submit', async function (e) {
      console.log('Upload form submitted');
      e.preventDefault();
      e.stopPropagation();
      await handleUpload();
    });

    console.log('Event listeners attached successfully');
  } else {
    console.error('Upload form not found!');
  }

  async function handleUpload(action = null) {
    console.log('handleUpload function called with action:', action);

    const file = fileInput.files[0];
    if (!file) {
      showMessage('Please select a file', 'danger');
      return;
    }

    const formData = new FormData();
    formData.append('file', file);

    // Add action parameter if specified
    if (action) {
      formData.append('action', action);
    }

    try {
      showProgress(true);
      clearMessages();

      const response = await fetch(
        window.resourcesData.uploadUrl,
        /** @type {RequestInit} */ ({
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': window.resourcesData.csrfTokenUpload,
          },
        })
      );

      const result = await response.json();
      showProgress(false);

      if (result.success) {
        showMessage(window.resourcesData.translations.uploadSuccess, 'success');
        fileInput.value = '';
        if (fileInputDisplay) {
          fileInputDisplay.value = '';
        }
        await refreshFileList();
      } else if (result.conflict) {
        // File conflict detected - show modal
        showFileConflictModal(
          result?.existingFile || '',
          result?.isCustomName || false
        );
      } else {
        showMessage(
          `${window.resourcesData.translations.uploadError}: ${result.message}`,
          'danger'
        );
      }
    } catch (error) {
      showProgress(false);
      showMessage(
        `${window.resourcesData.translations.uploadError}: ${error.message}`,
        'danger'
      );
    }
  }

  function showFileConflictModal(existingFileName, isCustomName = false) {
    console.log(
      'showFileConflictModal called with:',
      existingFileName,
      'isCustomName:',
      isCustomName
    );

    // Show appropriate messages based on conflict type
    const defaultMessage = document.getElementById('defaultConflictMessage');
    const customMessage = document.getElementById('customConflictMessage');
    const defaultText = document.getElementById('defaultConflictText');
    const customText = document.getElementById('customConflictText');

    if (isCustomName) {
      // Show custom conflict messages
      if (defaultMessage) defaultMessage.style.display = 'none';
      if (customMessage) customMessage.style.display = 'block';
      if (defaultText) defaultText.style.display = 'none';
      if (customText) customText.style.display = 'block';

      // Set custom filename
      const conflictFileNameCustom = document.getElementById(
        'conflictFileNameCustom'
      );
      if (conflictFileNameCustom) {
        conflictFileNameCustom.textContent = existingFileName;
      }
    } else {
      // Show default conflict messages
      if (defaultMessage) defaultMessage.style.display = 'block';
      if (customMessage) customMessage.style.display = 'none';
      if (defaultText) defaultText.style.display = 'block';
      if (customText) customText.style.display = 'none';

      // Set default filename
      const conflictFileNameElement =
        document.getElementById('conflictFileName');
      if (conflictFileNameElement) {
        conflictFileNameElement.textContent = existingFileName;
      }
    }

    // Extract extension and set it in the modal
    const extension = existingFileName.split('.').pop();
    const fileExtensionElement = document.getElementById('fileExtension');
    if (fileExtensionElement) {
      fileExtensionElement.textContent = '.' + extension;
    }

    // Set default custom name (without extension)
    const customFileNameInput = document.getElementById('customFileName');
    if (customFileNameInput) {
      const nameWithoutExt = existingFileName.replace('.' + extension, '');
      if (isCustomName) {
        // If it's a custom name conflict, clear the input so user can try again
        customFileNameInput.value = '';
        customFileNameInput.placeholder =
          window.resourcesData.translations?.chooseAnotherName ||
          'Veuillez choisir un autre nom';
      } else {
        customFileNameInput.value = nameWithoutExt + '_copy';
        customFileNameInput.placeholder =
          window.resourcesData.translations?.enterFileName ||
          'Entrez le nom du fichier';
      }
    }

    // Reset modal state
    const customRenameSection = document.getElementById('customRenameSection');
    if (customRenameSection) {
      if (isCustomName) {
        // If it's a custom name conflict, show the rename section directly
        customRenameSection.style.display = 'block';
        setTimeout(() => customFileNameInput.focus(), 100);

        // Add auto-generate option for custom name conflicts
        const existingAutoBtn =
          customRenameSection.querySelector('.auto-generate-btn');
        if (!existingAutoBtn) {
          const autoGenerateBtn = document.createElement('button');
          autoGenerateBtn.className =
            'btn btn-outline-secondary btn-sm mt-2 auto-generate-btn';
          autoGenerateBtn.type = 'button';
          const generateText =
            window.resourcesData?.translations?.generateAutomatically ||
            'Générer automatiquement';
          autoGenerateBtn.innerHTML = `<i class="fas fa-magic me-2"></i>${generateText}`;
          autoGenerateBtn.addEventListener('click', async function () {
            // Generate a unique name and show it in the input
            await generateAndShowUniqueName(
              customFileNameInput,
              existingFileName
            );
          });
          customRenameSection.appendChild(autoGenerateBtn);
        }
      } else {
        customRenameSection.style.display = 'none';
        // Remove auto-generate button if it exists
        const existingAutoBtn =
          customRenameSection.querySelector('.auto-generate-btn');
        if (existingAutoBtn) {
          existingAutoBtn.remove();
        }
      }
    }

    // Reset button states
    const renameFileBtn = document.getElementById('renameFileBtn');
    if (renameFileBtn) {
      renameFileBtn.innerHTML =
        '<i class="fas fa-check me-2"></i>' +
        window.resourcesData.translations.confirmRename;
      if (isCustomName) {
        renameFileBtn.style.display = 'inline-block';
      } else {
        renameFileBtn.style.display = 'none';
      }
    }

    // Remove selected class from all cards
    document.querySelectorAll('.action-card').forEach(card => {
      card.classList.remove('selected');
    });

    // If it's a custom name conflict, select the rename card and disable replace option
    if (isCustomName) {
      const renameCard = document.querySelector(
        '.action-card[data-action="rename"]'
      );
      if (renameCard) {
        renameCard.classList.add('selected');
      }
      // Disable the replace card for custom name conflicts
      const replaceCard = document.querySelector(
        '.action-card[data-action="replace"]'
      );
      if (replaceCard) {
        replaceCard.style.opacity = '0.5';
        replaceCard.style.pointerEvents = 'none';
        replaceCard.style.cursor = 'not-allowed';
      }
    } else {
      // For non-custom conflicts, ensure replace card is enabled
      const replaceCard = document.querySelector(
        '.action-card[data-action="replace"]'
      );
      if (replaceCard) {
        replaceCard.style.opacity = '1';
        replaceCard.style.pointerEvents = 'auto';
        replaceCard.style.cursor = 'pointer';
      }
    }

    if (fileConflictModal) {
      fileConflictModal.show();
    } else {
      console.error('File conflict modal not initialized');
    }
  }

  // Copy URL buttons
  document.addEventListener('click', function (e) {
    if (e.target.closest('.copy-url-btn')) {
      console.log('Copy URL button clicked!');
      const button = e.target.closest('.copy-url-btn');
      const url = button.getAttribute('data-url');
      console.log('URL to copy:', url);
      copyToClipboard(url).catch(console.error);
    }
  });

  // Insert image in CKEditor buttons
  document.addEventListener('click', function (e) {
    console.log('Click detected on:', e.target);
    if (e.target.closest('.insert-image-btn')) {
      console.log('Insert image button clicked!');
      const button = e.target.closest('.insert-image-btn');
      const url = button.getAttribute('data-url');
      const filename = button.getAttribute('data-filename');
      console.log('Image URL:', url, 'Filename:', filename);
      insertImageInCKEditor(url, filename);
    }
  });

  // Insert link for PDF files
  document.addEventListener('click', function (e) {
    console.log('Click detected on:', e.target);
    if (e.target.closest('.insert-link-btn')) {
      console.log('Insert link button clicked!');
      const button = e.target.closest('.insert-link-btn');
      const url = button.getAttribute('data-url');
      const filename = button.getAttribute('data-filename');
      console.log('PDF URL:', url, 'Filename:', filename);
      insertLinkInCKEditor(url, filename);
    }
  });

  // Delete file buttons
  document.addEventListener('click', function (e) {
    console.log('Click detected on delete area:', e.target);
    const deleteBtn = e.target.closest('.delete-file-btn');
    console.log('Delete button found:', deleteBtn);

    if (deleteBtn) {
      const filename = deleteBtn.getAttribute('data-filename');
      console.log('Filename to delete:', filename);

      // Lire les données d'utilisation directement depuis le HTML (pré-calculées par PHP)
      const usageData = JSON.parse(
        deleteBtn.getAttribute('data-usage') || '{}'
      );
      console.log('Usage data from HTML:', usageData);

      fileToDeleteName = filename;
      if (fileToDelete) {
        fileToDelete.textContent = filename;
      }

      // Show modal and display usage info (pas d'appel API)
      if (deleteConfirmModal) {
        console.log('Showing modal for file:', filename);
        deleteConfirmModal.show();

        // Afficher les infos d'utilisation (données déjà disponibles)
        displayResourceUsage(usageData);
      } else {
        console.error('Modal not initialized');
      }
    }
  });

  // Confirm delete handler
  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', async function () {
      if (!fileToDeleteName) return;

      try {
        const deleteUrl = window.resourcesData.deleteUrl.replace(
          '__FILENAME__',
          encodeURIComponent(fileToDeleteName)
        );

        const response = await fetch(deleteUrl, {
          method: 'DELETE',
          headers: {
            'X-CSRF-Token': window.resourcesData.csrfTokenDelete,
          },
        });

        const result = await response.json();

        if (result.success) {
          showMessage(
            window.resourcesData.translations.deleteSuccess,
            'success'
          );
          if (deleteConfirmModal) {
            deleteConfirmModal.hide();
          }
          await refreshFileList();
        } else {
          showMessage(
            `${window.resourcesData.translations.deleteError}: ${result.message}`,
            'danger'
          );
        }
      } catch (error) {
        showMessage(
          `${window.resourcesData.translations.deleteError}: ${error.message}`,
          'danger'
        );
      }

      fileToDeleteName = null;
    });
  }

  // Action card click handlers
  document.addEventListener('click', function (e) {
    const actionCard = e.target.closest('.action-card');
    if (actionCard) {
      const action = actionCard.getAttribute('data-action');

      // Remove selected class from all cards
      document.querySelectorAll('.action-card').forEach(card => {
        card.classList.remove('selected');
      });

      // Add selected class to clicked card
      actionCard.classList.add('selected');

      if (action === 'replace') {
        // Hide modal and replace file
        if (fileConflictModal) {
          fileConflictModal.hide();
        }
        handleUpload('replace').catch(console.error);
      } else if (action === 'rename') {
        // Show custom rename section
        const customRenameSection = document.getElementById(
          'customRenameSection'
        );
        if (customRenameSection) {
          customRenameSection.style.display = 'block';
          // Focus on input field
          const customFileNameInput = document.getElementById('customFileName');
          if (customFileNameInput) {
            setTimeout(() => customFileNameInput.focus(), 100);
          }

          // Show the rename confirmation button
          const renameFileBtn = document.getElementById('renameFileBtn');
          if (renameFileBtn) {
            renameFileBtn.innerHTML =
              '<i class="fas fa-check me-2"></i>' +
              window.resourcesData.translations.confirmRename;
            renameFileBtn.style.display = 'inline-block';
          }
        }
      }
    }
  });

  // Keep the confirm rename button handler
  if (renameFileBtn) {
    renameFileBtn.addEventListener('click', function () {
      const customFileName = document
        .getElementById('customFileName')
        .value.trim();
      const fileExtension =
        document.getElementById('fileExtension').textContent;

      if (!customFileName) {
        showMessage(
          window.resourcesData.translations.pleaseEnterFileName,
          'danger'
        );
        return;
      }

      // Hide modal and upload with custom name
      if (fileConflictModal) {
        fileConflictModal.hide();
      }

      handleUploadWithCustomName(customFileName + fileExtension).catch(
        console.error
      );
    });
  }

  async function checkFileExists(filename) {
    try {
      const formData = new FormData();
      formData.append('filename', filename);

      const response = await fetch(
        window.resourcesData.checkExistsUrl,
        /** @type {RequestInit} */ ({
          method: 'POST',
          body: formData,
        })
      );

      const result = await response.json();
      return result.success ? result.exists : false;
    } catch (error) {
      console.error('Error checking file existence:', error);
      return false;
    }
  }

  async function handleUploadWithCustomName(customFileName) {
    console.log('handleUploadWithCustomName called with:', customFileName);

    const file = fileInput.files[0];
    if (!file) {
      console.log('No file selected');
      return;
    }

    // Check if the custom filename already exists before attempting upload
    console.log('Checking if custom filename exists:', customFileName);
    const fileExists = await checkFileExists(customFileName);
    console.log('File exists check result:', fileExists);

    if (fileExists) {
      console.log('Custom filename already exists, showing conflict modal');
      // Hide current modal first, then show new one
      if (fileConflictModal) {
        fileConflictModal.hide();
        // Wait a bit for the modal to close completely before showing the new one
        setTimeout(() => {
          showFileConflictModal(customFileName, true);
        }, 300);
      } else {
        showFileConflictModal(customFileName, true);
      }
      return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('action', 'custom');
    formData.append('customFileName', customFileName);

    console.log('Sending custom upload request:', {
      fileName: file.name,
      action: 'custom',
      customFileName: customFileName,
    });

    try {
      showProgress(true);
      clearMessages();

      const response = await fetch(
        window.resourcesData.uploadUrl,
        /** @type {RequestInit} */ ({
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
        })
      );

      const result = await response.json();
      console.log('Custom upload response:', result);
      showProgress(false);

      if (result.success) {
        console.log('Upload successful');
        showMessage(window.resourcesData.translations.uploadSuccess, 'success');
        fileInput.value = '';
        if (fileInputDisplay) {
          fileInputDisplay.value = '';
        }
        await refreshFileList();
      } else if (result.conflict) {
        console.log('Custom name conflict detected, showing modal again');
        // Custom name conflict - show modal again with isCustomName flag
        showFileConflictModal(result?.existingFile || '', true);
      } else {
        console.log('Upload failed:', result.message);
        showMessage(
          `${window.resourcesData.translations.uploadError}: ${result.message}`,
          'danger'
        );
      }
    } catch (error) {
      console.error('Upload error:', error);
      showProgress(false);
      showMessage(
        `${window.resourcesData.translations.uploadError}: ${error.message}`,
        'danger'
      );
    }
  }

  // Helper functions
  function showProgress(show) {
    if (show) {
      uploadProgress.style.display = 'block';
      uploadProgress.querySelector('.progress-bar').style.width = '100%';
    } else {
      uploadProgress.style.display = 'none';
      uploadProgress.querySelector('.progress-bar').style.width = '0%';
    }
  }

  function showMessage(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

    uploadMessages.appendChild(alertDiv);

    // Auto-remove success messages after 5 seconds
    if (type === 'success') {
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
    }
  }

  function clearMessages() {
    uploadMessages.innerHTML = '';
  }

  async function copyToClipboard(text) {
    console.log('copyToClipboard called with:', text);

    // Modern clipboard API
    if (navigator.clipboard && window.isSecureContext) {
      try {
        await navigator.clipboard.writeText(text);
        console.log('Clipboard API succeeded');
        showMessage(window.resourcesData.translations.copySuccess, 'success');
        return;
      } catch (error) {
        console.warn('Clipboard API failed, trying fallback:', error);
      }
    } else {
      console.log('Clipboard API not available, using fallback');
    }

    // Fallback using execCommand (deprecated but still works)
    try {
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.left = '-9999px';
      textArea.style.top = '-9999px';
      textArea.setAttribute('readonly', '');
      document.body.appendChild(textArea);

      // Select the text
      textArea.focus();
      textArea.select();
      textArea.setSelectionRange(0, 99999);

      // Execute copy command
      const successful = document.execCommand('copy');
      document.body.removeChild(textArea);

      if (successful) {
        console.log('execCommand copy succeeded');
        showMessage(window.resourcesData.translations.copySuccess, 'success');
      } else {
        console.warn('execCommand copy returned false');
        showMessage(window.resourcesData.translations.copyError, 'danger');
      }
    } catch (error) {
      console.error('Fallback copy failed:', error);
      showMessage(window.resourcesData.translations.copyError, 'danger');
    }
  }

  async function refreshFileList() {
    console.log('refreshFileList called');
    try {
      const response = await fetch(window.resourcesData.listUrl);
      const result = await response.json();
      console.log('File list response:', result);

      if (result.success) {
        console.log('Files received from server:', result.files);
        updateFileTable(result.files);
      } else {
        console.error('Server returned error:', result);
      }
    } catch (error) {
      console.error('Error refreshing file list:', error);
    }
  }

  function updateFileTable(files) {
    try {
      console.log('updateFileTable called with files:', files);

      // Find specifically the files table card (second card, not the upload card)
      const allCards = document.querySelectorAll('.card');
      const filesCard = allCards.length >= 2 ? allCards[1] : allCards[0];
      const filesCardBody = filesCard
        ? filesCard.querySelector('.card-body')
        : null;

      console.log('Files card found:', filesCard);
      console.log('Files card body found:', filesCardBody);

      if (files.length === 0) {
        console.log('No files, showing empty state');
        // Show empty state - replace only the files card body content
        if (filesCardBody) {
          filesCardBody.innerHTML = `
                  <div class="text-center py-4">
                      <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                      <p class="text-muted">${window.resourcesData?.translations?.noFiles || 'No files available at the moment'}</p>
                  </div>
              `;
        }
        return;
      }

      console.log('Files found, updating table');

      // Check if table exists
      let currentTable = document.getElementById('filesTable');
      let tbody = currentTable ? currentTable.querySelector('tbody') : null;

      // Rebuild table if it was replaced with empty state OR if no table exists at all
      if (
        !currentTable ||
        !tbody ||
        filesCardBody.querySelector('.text-center')
      ) {
        console.log(
          'Recreating table structure - table missing or empty state detected'
        );
        // Recreate the table structure
        if (filesCardBody) {
          filesCardBody.innerHTML = `
                  <div class="table-responsive">
                      <table class="table table-striped" id="filesTable">
                          <thead>
                              <tr>
                                  <th>${window.resourcesData?.translations?.fileName || 'File Name'}</th>
                                  <th>${window.resourcesData?.translations?.publicUrl || 'Public URL'}</th>
                                  <th>${window.resourcesData?.translations?.lastModified || 'Last Modified'}</th>
                                  <th>${window.resourcesData?.translations?.fileSize || 'File Size'}</th>
                                  <th class="text-center">${window.resourcesData?.translations?.actions || 'Actions'}</th>
                              </tr>
                          </thead>
                          <tbody></tbody>
                      </table>
                  </div>
              `;
          // Update references to the new table
          currentTable = document.getElementById('filesTable');
          tbody = currentTable ? currentTable.querySelector('tbody') : null;
          console.log('After recreation - currentTable:', currentTable);
          console.log('After recreation - tbody:', tbody);
          // Update the global filesTable variable if it exists
          if (typeof window.filesTable !== 'undefined') {
            window.filesTable = currentTable;
          }
        }
      }

      console.log('About to check tbody validity...');

      if (!tbody) {
        console.error('Still no tbody found after recreation');
        return;
      }

      console.log('tbody found successfully:', tbody);

      // Clear existing rows
      tbody.innerHTML = '';
      console.log('Table cleared, adding', files.length, 'files');

      console.log('Starting file loop...');
      files.forEach((file, index) => {
        console.log(`Processing file ${index + 1}/${files.length}:`, file.name);

        const row = document.createElement('tr');
        row.setAttribute('data-filename', file.name);

        // Format date according to locale (matching the Twig template)
        const modifiedDate = formatDateForLocale(
          new Date(file.modified * 1000)
        );
        const fileSize = formatFileSize(file.size);
        const fileIcon = getFileIcon(file.name);

        row.innerHTML = `
                  <td>
                      <i class="fas ${fileIcon} me-2"></i>
                      <strong>${escapeHtml(file.name)}</strong>
                  </td>
                  <td>
                      <div class="input-group input-group-sm">
                          <input type="text" class="form-control" value="${escapeHtml(file.url)}" readonly>
                          <button class="btn btn-outline-secondary copy-url-btn" type="button" 
                                  data-url="${escapeHtml(file.url)}" 
                                  title="Copy URL">
                              <i class="fas fa-copy"></i>
                          </button>
                          ${
                            isImageFile(file.name)
                              ? `
                          <button class="btn btn-outline-primary insert-image-btn" type="button"
                                  data-url="${escapeHtml(file.url)}"
                                  data-filename="${escapeHtml(file.name)}"
                                  title="Insert in Editor">
                              <i class="fas fa-plus"></i>
                          </button>
                          `
                              : isPdfFile(file.name)
                                ? `
                          <button class="btn btn-outline-success insert-link-btn" type="button"
                                  data-url="${escapeHtml(file.url)}"
                                  data-filename="${escapeHtml(file.name)}"
                                  title="${window.resourcesData?.translations?.insertLink || 'Insert Link'}">
                              <i class="fas fa-link"></i>
                          </button>
                          `
                                : ''
                          }
                      </div>
                  </td>
                  <td>${modifiedDate}</td>
                  <td>${fileSize}</td>
                  <td class="text-center">
                      <button class="btn btn-sm btn-danger delete-file-btn" 
                              data-filename="${escapeHtml(file.name)}"
                              title="Delete File">
                          <i class="fas fa-trash"></i>
                      </button>
                  </td>
              `;

        tbody.appendChild(row);
        console.log(`File ${file.name} added to table successfully`);
      });

      console.log('updateFileTable completed successfully!');
    } catch (error) {
      console.error('Error in updateFileTable:', error);
      console.error('Error stack:', error.stack);
    }
  }

  function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';

    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();

    const iconMap = {
      // Images
      png: 'fa-image',
      jpg: 'fa-image',
      jpeg: 'fa-image',
      gif: 'fa-image',
      tif: 'fa-image',
      tiff: 'fa-image',

      // Documents
      pdf: 'fa-file-pdf',
      doc: 'fa-file-word',
      docx: 'fa-file-word',
      rtf: 'fa-file-word',
      odt: 'fa-file-word',

      // Spreadsheets
      xls: 'fa-file-excel',
      xlsx: 'fa-file-excel',
      ods: 'fa-file-excel',

      // Archives
      zip: 'fa-file-archive',
      rar: 'fa-file-archive',
      gz: 'fa-file-archive',
      '7z': 'fa-file-archive',
      tar: 'fa-file-archive',
      bz: 'fa-file-archive',
      bz2: 'fa-file-archive',

      // Text files
      txt: 'fa-file-alt',
      md: 'fa-file-alt',

      // LaTeX
      tex: 'fa-file-code',
      bib: 'fa-file-code',
      cls: 'fa-file-code',
      sty: 'fa-file-code',
    };

    return iconMap[extension] || 'fa-file';
  }

  function isImageFile(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    return ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'].includes(extension);
  }

  function isPdfFile(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    return extension === 'pdf';
  }

  function insertImageInCKEditor(url, filename) {
    console.log('insertImageInCKEditor called with:', url, filename);
    console.log('window.parent !== window:', window.parent !== window);
    console.log(
      'typeof window.insertImageIntoEditor:',
      typeof window.insertImageIntoEditor
    );

    // Try to communicate with CKEditor using postMessage
    if (window.parent !== window) {
      console.log('Sending postMessage to parent window');
      // We're in an iframe, send message to parent
      window.parent.postMessage(
        {
          type: 'insertImage',
          url: url,
          alt: filename,
        },
        '*'
      );
    } else {
      // Try to find CKEditor instance on the current page
      if (typeof window.insertImageIntoEditor === 'function') {
        console.log('Calling window.insertImageIntoEditor');
        window.insertImageIntoEditor(url, filename);
      } else {
        console.log(
          'CKEditor not found on current page, trying to communicate with opener window'
        );
        console.log('window.opener:', window.opener);
        console.log('window.opener exists:', !!window.opener);
        console.log(
          'window.opener.closed:',
          window.opener ? window.opener.closed : 'N/A'
        );

        // Try to send message to opener window (if this was opened from another page)
        if (window.opener && !window.opener.closed) {
          console.log('Sending message to opener window');
          window.opener.postMessage(
            {
              type: 'insertImage',
              url: url,
              alt: filename,
            },
            '*'
          );
          showImageInsertModal(filename);
        } else {
          console.log(
            'No opener window available. Trying localStorage communication'
          );

          // Store the image data in localStorage for the editor to pick up
          const imageData = {
            type: 'insertImage',
            url: url,
            alt: filename,
            timestamp: Date.now(),
          };

          localStorage.setItem('pendingImageInsert', JSON.stringify(imageData));
          console.log('Image data stored in localStorage:', imageData);
          showImageInsertModal(filename);

          // Also try to trigger a storage event
          window.dispatchEvent(
            new StorageEvent('storage', {
              key: 'pendingImageInsert',
              newValue: JSON.stringify(imageData),
              oldValue: null,
              storageArea: localStorage,
            })
          );
        }
      }
    }
  }

  function insertLinkInCKEditor(url, filename) {
    console.log('insertLinkInCKEditor called with:', url, filename);
    console.log('Current window context:', {
      hasParent: window.parent !== window,
      hasOpener: !!window.opener,
      openerClosed: window.opener ? window.opener.closed : 'N/A',
      insertLinkIntoEditor: typeof window.insertLinkIntoEditor,
      insertImageIntoEditor: typeof window.insertImageIntoEditor,
    });

    // Generate unique insert ID to prevent duplicates
    const insertId = `link_${Date.now()}_${Math.random().toString(36).substring(2, 11)}`;
    console.log('Generated insert ID:', insertId);

    // Create link HTML (escaped for XSS protection)
    const linkHtml = `<a href="${escapeHtml(url)}" target="_blank">${escapeHtml(filename)}</a>`;

    // Try to communicate with CKEditor using postMessage
    if (window.parent !== window) {
      console.log('Sending link postMessage to parent window');
      // We're in an iframe, send message to parent
      window.parent.postMessage(
        {
          type: 'insertLink',
          url: url,
          text: filename,
          html: linkHtml,
          insertId: insertId,
        },
        '*'
      );
      showLinkInsertModal(filename);
    } else {
      // Try to find CKEditor instance on the current page
      if (typeof window.insertLinkIntoEditor === 'function') {
        console.log('Calling window.insertLinkIntoEditor');
        window.insertLinkIntoEditor(url, filename, insertId);
        showLinkInsertModal(filename);
      } else if (typeof window.insertImageIntoEditor === 'function') {
        console.log('Calling window.insertImageIntoEditor with link HTML');
        // Fallback: try to insert as HTML using the existing function
        window.insertImageIntoEditor(linkHtml, filename);
        showLinkInsertModal(filename);
      } else {
        console.log(
          'CKEditor not found on current page, trying to communicate with opener window'
        );

        // Try to send message to opener window (if this was opened from another page)
        if (window.opener && !window.opener.closed) {
          console.log('Sending link message to opener window');
          window.opener.postMessage(
            {
              type: 'insertLink',
              url: url,
              text: filename,
              html: linkHtml,
              insertId: insertId,
            },
            '*'
          );
          showLinkInsertModal(filename);
        } else {
          console.log(
            'No opener window available. Trying localStorage communication'
          );

          // Store the link data in localStorage for the editor to pick up
          const linkData = {
            type: 'insertLink',
            url: url,
            text: filename,
            html: linkHtml,
            insertId: insertId,
            timestamp: Date.now(),
          };

          localStorage.setItem('pendingLinkInsert', JSON.stringify(linkData));
          console.log('Link data stored in localStorage:', linkData);
          showLinkInsertModal(filename);

          // Also try to trigger a storage event
          window.dispatchEvent(
            new StorageEvent('storage', {
              key: 'pendingLinkInsert',
              newValue: JSON.stringify(linkData),
              oldValue: null,
              storageArea: localStorage,
            })
          );
        }
      }
    }
  }

  function showLinkInsertModal(filename) {
    // Update modal content with filename for the specific link modal
    const linkNameElement = document.getElementById('insertedLinkName');
    if (linkNameElement) {
      linkNameElement.textContent = filename;
    }

    // Show the specific link modal
    if (linkInsertModal) {
      linkInsertModal.show();
    } else {
      console.error('Link insert modal not initialized');
    }
  }

  function showImageInsertModal(filename) {
    // Update modal content with filename
    const imageNameElement = document.getElementById('insertedImageName');
    if (imageNameElement) {
      imageNameElement.textContent = filename;
    }

    // Show the modal
    if (imageInsertModal) {
      imageInsertModal.show();
    } else {
      console.error('Image insert modal not initialized');
    }
  }

  function formatDateForLocale(date) {
    // Get the current locale from multiple sources
    let locale = 'fr'; // default

    // Try to get locale from HTML lang attribute
    if (document.documentElement.lang) {
      locale = document.documentElement.lang;
    }
    // Try to get from URL parameter
    else if (window.location.search.includes('_locale=en')) {
      locale = 'en';
    }
    // Try to get from Symfony app request locale if available
    else if (window.resourcesData && window.resourcesData.locale) {
      locale = window.resourcesData.locale;
    }

    console.log('Date formatting using locale:', locale);

    if (locale === 'en') {
      // English: 12-hour format with AM/PM - YYYY-MM-DD h:mm AM/PM
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');

      let hours = date.getHours();
      const minutes = String(date.getMinutes()).padStart(2, '0');
      const ampm = hours >= 12 ? 'PM' : 'AM';

      hours = hours % 12;
      hours = hours ? hours : 12; // 0 should be 12

      return `${year}-${month}-${day} ${hours}:${minutes} ${ampm}`;
    } else {
      // French: 24-hour format - YYYY-MM-DD HH:mm
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');

      return `${year}-${month}-${day} ${hours}:${minutes}`;
    }
  }

  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      ["'"]: '&#039;',
    };

    return text.replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  }

  // Display resource usage in modal (données pré-calculées par PHP, pas d'appel API)
  function displayResourceUsage(usageData) {
    console.log('Displaying resource usage:', usageData);

    const warningSection = document.getElementById('usageWarningSection');
    const safeToDeleteSection = document.getElementById('safeToDeleteSection');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Reset sections
    if (warningSection) {
      warningSection.style.display = 'none';
    }
    if (safeToDeleteSection) {
      safeToDeleteSection.style.display = 'none';
    }

    if (usageData?.inUse) {
      // File is in use - show warning (combine pages and news)
      showResourceUsageWarning([
        ...(usageData.pages || []),
        ...(usageData.news || []),
      ]);

      // Change button text to indicate warning
      if (confirmDeleteBtn) {
        const deleteAnywayText =
          window.resourcesData?.translations?.deleteAnyway || 'Delete anyway';
        confirmDeleteBtn.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${deleteAnywayText}`;
        confirmDeleteBtn.className = 'btn btn-warning';
      }
    } else {
      // File is not in use - show safe to delete message
      console.log('File is not in use, safe to delete');

      // Show safe to delete message
      if (safeToDeleteSection) {
        safeToDeleteSection.style.display = 'block';
      }

      // Ensure button is normal
      if (confirmDeleteBtn) {
        const deleteText =
          window.resourcesData?.translations?.delete || 'Delete';
        confirmDeleteBtn.innerHTML = `<i class="fas fa-trash me-2"></i>${deleteText}`;
        confirmDeleteBtn.className = 'btn btn-danger';
      }
    }
  }

  // Show resource usage warning in modal
  function showResourceUsageWarning(items) {
    console.log('Showing usage warning for items:', items);

    const warningSection = document.getElementById('usageWarningSection');
    const usageDetailsList = document.getElementById('usageDetailsList');

    if (!warningSection || !usageDetailsList) {
      console.error('Warning elements not found');
      return;
    }

    // Separate pages and news
    const pages = items.filter(item => item.type === 'page' || !item.type);
    const news = items.filter(item => item.type === 'news');

    let html = '<div class="mt-3">';

    // Build the list of pages
    if (pages.length > 0) {
      const pagesUsingFileText =
        window.resourcesData?.translations?.pagesUsingFile ||
        'Pages using this file:';
      html += `<strong><i class="fas fa-file-alt me-1"></i>${pagesUsingFileText}</strong><ul class="mt-2 mb-3">`;

      pages.forEach(page => {
        const currentLocale = getCurrentLocale();
        let pageTitle = page?.page_code || 'Unknown page';
        if (page?.title) {
          pageTitle =
            page.title[currentLocale] ||
            page.title.en ||
            page.title.fr ||
            page?.page_code ||
            'Unknown page';
        }

        html += `<li><strong>${escapeHtml(pageTitle)}</strong> (${escapeHtml(page?.page_code || 'Unknown')})`;
        if (page?.locationCount > 1) {
          html += ` - ${page.locationCount} references`;
        }
        html += '</li>';
      });

      html += '</ul>';
    }

    // Build the list of news
    if (news.length > 0) {
      const newsUsingFileText =
        window.resourcesData?.translations?.newsUsingFile ||
        'News using this file:';
      html += `<strong><i class="fas fa-newspaper me-1"></i>${newsUsingFileText}</strong><ul class="mt-2">`;

      news.forEach(newsItem => {
        const currentLocale = getCurrentLocale();
        let newsTitle = 'News #' + (newsItem?.news_id || 'Unknown');
        if (newsItem?.title) {
          newsTitle =
            newsItem.title[currentLocale] ||
            newsItem.title.en ||
            newsItem.title.fr ||
            newsTitle;
        }

        html += `<li><strong>${escapeHtml(newsTitle)}</strong>`;
        if (newsItem?.locationCount > 1) {
          html += ` - ${newsItem.locationCount} references`;
        }
        html += '</li>';
      });

      html += '</ul>';
    }

    html += '</div>';

    usageDetailsList.innerHTML = html;
    warningSection.style.display = 'block';
  }

  // Get current locale (copied from other part of the code)
  function getCurrentLocale() {
    const locale =
      document.documentElement.lang ||
      window.location.pathname.split('/')[1] ||
      'en';

    // Validate the locale (only EN and FR are supported)
    return ['en', 'fr'].includes(locale) ? locale : 'en';
  }

  // Generate and show a unique filename in the input field
  async function generateAndShowUniqueName(inputElement, existingFileName) {
    console.log('Generating unique name for:', existingFileName);

    // Extract name and extension
    const extension = existingFileName.split('.').pop();
    const nameWithoutExt = existingFileName.replace('.' + extension, '');

    // Show loading state on button
    const autoBtn = document.querySelector('.auto-generate-btn');
    if (autoBtn) {
      autoBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin me-2"></i>Génération...';
      autoBtn.disabled = true;
    }

    try {
      // Try different patterns until we find a unique name
      let uniqueName = null;
      let counter = 1;
      const maxAttempts = 50;

      while (counter <= maxAttempts) {
        // Generate different patterns
        let testName;
        if (counter === 1) {
          testName = `${nameWithoutExt}_copy`;
        } else if (counter <= 10) {
          testName = `${nameWithoutExt}_copy_${counter}`;
        } else if (counter <= 20) {
          testName = `${nameWithoutExt}_${counter}`;
        } else if (counter <= 30) {
          testName = `${nameWithoutExt}_new_${counter - 20}`;
        } else {
          // Use timestamp for uniqueness
          const timestamp = Date.now().toString().slice(-6);
          testName = `${nameWithoutExt}_${timestamp}`;
        }

        const testFullName = `${testName}.${extension}`;
        console.log(`Testing name ${counter}:`, testFullName);

        // Check if this name exists
        const exists = await checkFileExists(testFullName);
        if (!exists) {
          uniqueName = testName;
          console.log('Found unique name:', testFullName);
          break;
        }

        counter++;
      }

      // Update the input field with the generated name
      if (uniqueName) {
        inputElement.value = uniqueName;
        inputElement.focus();
        inputElement.select(); // Select the text so user can see it clearly

        // Add visual feedback
        inputElement.style.backgroundColor = '#d4edda'; // Light green
        setTimeout(() => {
          inputElement.style.backgroundColor = '';
        }, 2000);

        console.log('Generated unique name:', uniqueName);
      } else {
        // Fallback with timestamp if all attempts failed
        const timestamp = Date.now();
        const fallbackName = `${nameWithoutExt}_${timestamp}`;
        inputElement.value = fallbackName;
        inputElement.focus();
        console.log('Used fallback name:', fallbackName);
      }
    } catch (error) {
      console.error('Error generating unique name:', error);

      // Fallback: simple timestamp-based name
      const timestamp = Date.now();
      inputElement.value = `${nameWithoutExt}_${timestamp}`;
      inputElement.focus();

      showMessage(
        'Erreur lors de la génération. Nom par défaut utilisé.',
        'warning'
      );
    } finally {
      // Restore button state
      if (autoBtn) {
        const generateText =
          window.resourcesData?.translations?.generateAutomatically ||
          'Générer automatiquement';
        autoBtn.innerHTML = `<i class="fas fa-magic me-2"></i>${generateText}`;
        autoBtn.disabled = false;
      }
    }
  }
});
