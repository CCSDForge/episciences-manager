document.addEventListener('DOMContentLoaded', function () {
  console.log('Resources script loaded, looking for upload form...');
  console.log('DOM content loaded, document ready state:', document.readyState);

  const uploadForm = document.getElementById('uploadForm');
  console.log('Upload form element:', uploadForm);

  const fileInput = document.getElementById('fileInput');
  console.log('File input element:', fileInput);

  const uploadProgress = document.getElementById('uploadProgress');
  const uploadMessages = document.getElementById('uploadMessages');
  const filesTable = document.getElementById('filesTable');

  console.log('Looking for deleteConfirmModal...');
  const modalElement = document.getElementById('deleteConfirmModal');
  console.log('Modal element found:', modalElement);

  let deleteConfirmModal = null;
  try {
    deleteConfirmModal = modalElement
      ? new window.bootstrap.Modal(modalElement)
      : null;
    console.log('Modal initialized successfully');
  } catch (error) {
    console.error('Error initializing modal:', error);
  }

  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  const fileToDelete = document.getElementById('fileToDelete');

  let fileToDeleteName = null;

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

  async function handleUpload() {
    console.log('handleUpload function called');

    const file = fileInput.files[0];
    if (!file) {
      showMessage('Please select a file', 'danger');
      return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append(
      'overwrite',
      document.getElementById('overwriteFile').checked
    );

    try {
      showProgress(true);
      clearMessages();

      const response = await fetch(window.resourcesData.uploadUrl, {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();
      showProgress(false);

      if (result.success) {
        showMessage(window.resourcesData.translations.uploadSuccess, 'success');
        fileInput.value = '';
        document.getElementById('overwriteFile').checked = false;
        await refreshFileList();
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

  // Copy URL buttons
  document.addEventListener('click', function (e) {
    if (e.target.closest('.copy-url-btn')) {
      const button = e.target.closest('.copy-url-btn');
      const url = button.getAttribute('data-url');
      copyToClipboard(url);
    }
  });

  // Copy full URL buttons
  document.addEventListener('click', function (e) {
    if (e.target.closest('.copy-full-url-btn')) {
      const button = e.target.closest('.copy-full-url-btn');
      const relativeUrl = button.getAttribute('data-url');
      const fullUrl = new URL(relativeUrl, window.location.origin).href;
      copyToClipboard(fullUrl);
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

  // Delete file buttons
  document.addEventListener('click', function (e) {
    if (e.target.closest('.delete-file-btn')) {
      const button = e.target.closest('.delete-file-btn');
      const filename = button.getAttribute('data-filename');

      fileToDeleteName = filename;
      fileToDelete.textContent = filename;
      deleteConfirmModal.show();
    }
  });

  // Confirm delete handler
  confirmDeleteBtn.addEventListener('click', async function () {
    if (!fileToDeleteName) return;

    try {
      const deleteUrl = window.resourcesData.deleteUrl.replace(
        '__FILENAME__',
        encodeURIComponent(fileToDeleteName)
      );

      const response = await fetch(deleteUrl, {
        method: 'DELETE',
      });

      const result = await response.json();

      if (result.success) {
        showMessage(window.resourcesData.translations.deleteSuccess, 'success');
        deleteConfirmModal.hide();
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
    try {
      await navigator.clipboard.writeText(text);
      showMessage(window.resourcesData.translations.copySuccess, 'success');
    } catch {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();

      try {
        document.execCommand('copy');
        showMessage(window.resourcesData.translations.copySuccess, 'success');
      } catch {
        showMessage(window.resourcesData.translations.copyError, 'danger');
      }

      document.body.removeChild(textArea);
    }
  }

  async function refreshFileList() {
    try {
      const response = await fetch(window.resourcesData.listUrl);
      const result = await response.json();

      if (result.success) {
        updateFileTable(result.files);
      }
    } catch (error) {
      console.error('Error refreshing file list:', error);
    }
  }

  function updateFileTable(files) {
    const tbody = filesTable.querySelector('tbody');

    if (files.length === 0) {
      // Show empty state
      const card = document.querySelector('.card-body');
      card.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No files available at the moment</p>
                </div>
            `;
      return;
    }

    // Rebuild table if it was replaced with empty state
    if (!tbody) {
      location.reload(); // Simple solution - reload the page
      return;
    }

    tbody.innerHTML = '';

    files.forEach(file => {
      const row = document.createElement('tr');
      row.setAttribute('data-filename', file.name);

      const modifiedDate = new Date(file.modified * 1000).toLocaleString();
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
                        <button class="btn btn-outline-info copy-full-url-btn" type="button" 
                                data-url="${escapeHtml(file.url)}" 
                                title="Copy Full URL">
                            <i class="fas fa-link"></i>
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
    });
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
          showMessage(
            `Image sent to editor! You can close this window.`,
            'success'
          );
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
          showMessage(
            `Image ready for editor! Return to the editor tab.`,
            'success'
          );

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
});
