import { Markdown } from '@ckeditor/ckeditor5-markdown-gfm';
import {
  ClassicEditor,
  Essentials,
  Bold,
  Italic,
  Paragraph,
  Heading,
  List,
  Link,
  BlockQuote,
  Table,
  Undo,
  Indent,
  // CKEditor 4 group equivalents
  Alignment, // paragraph > alignment
  FindAndReplace, // editing > find and replace
  RemoveFormat, // basic styles > remove format
  FontSize, // styles > font size
  FontFamily, // styles > font family
  SpecialCharacters, // insert > special characters
  HorizontalLine, // insert > horizontal line
  Image, // insert > image
  ImageBlock, // image as block element
  ImageInline, // inline image
  ImageToolbar, // insert > image toolbar
  //ImageUpload, // insert > image upload
  ImageInsert, // insert > insert image via URL
  ImageInsertViaUrl, // insert > insert image via URL (new plugin)
  ImageTextAlternative,
  ImageCaption, // image captions
  CodeBlock, // insert > code block
  // Note: SourceEditing is not available in this version
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

let editorInstance = null;
let onChangeCallback = null;
let charCounterElement = null;
let maxCharLimit = 0;

// Track processed inserts to prevent duplicates
const processedInserts = new Set();

/**
 * Extract filename from a URL or path
 * @param {string} url - The URL or path to extract filename from
 * @returns {string|null} The filename or null if not found
 */
function extractFilenameFromUrl(url) {
  if (!url) return null;

  try {
    // Remove query string and hash
    const cleanUrl = url.split('?')[0].split('#')[0];
    // Get the last part of the path
    const parts = cleanUrl.split('/');
    const filename = parts[parts.length - 1];

    // Return filename if it looks valid (has an extension)
    if (filename && filename.includes('.')) {
      return filename;
    }
  } catch (e) {
    console.warn('Failed to extract filename from URL:', url, e);
  }

  return null;
}

/**
 * Get plain text character count from editor content
 */
function getCharCount() {
  if (!editorInstance) return 0;
  const content = editorInstance.getData();
  // Remove HTML tags and decode entities for accurate count
  const plainText = content
    .replace(/<[^>]*>/g, '')
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>');
  return plainText.length;
}

/**
 * Update the character counter display
 */
function updateCharCounter() {
  if (!charCounterElement || maxCharLimit <= 0) return;

  const count = getCharCount();
  charCounterElement.textContent = `${count} / ${maxCharLimit}`;

  // Change color based on limit
  charCounterElement.classList.remove(
    'text-danger',
    'text-warning',
    'text-muted',
    'fw-bold'
  );
  if (count > maxCharLimit) {
    charCounterElement.classList.add('text-danger', 'fw-bold');
  } else if (count > maxCharLimit * 0.9) {
    charCounterElement.classList.add('text-warning');
  } else {
    charCounterElement.classList.add('text-muted');
  }
}

/**
 * Create character counter element
 */
function createCharCounter(editorElement) {
  // Remove existing counter if any
  const existingCounter =
    editorElement.parentNode?.querySelector('.char-counter');
  if (existingCounter) existingCounter.remove();

  // Create new counter
  charCounterElement = document.createElement('div');
  charCounterElement.className = 'char-counter text-muted small mt-1 text-end';
  editorElement.parentNode?.appendChild(charCounterElement);

  updateCharCounter();
}

export function initializeCKEditor(
  elementId,
  placeholder = '',
  onChange = null,
  maxLength = 0
) {
  const element = document.getElementById(elementId);
  if (!element) {
    console.error(`Element with ID ${elementId} not found`);
    return null;
  }

  // Store the onChange callback and maxLength for later use
  onChangeCallback = onChange;
  maxCharLimit = maxLength;

  const config = {
    licenseKey: 'GPL', // Free license for open source usage
    plugins: [
      Essentials, // Essential plugins
      Paragraph,
      Heading,
      Undo,

      // Basic styles (basicstyles group)
      Bold,
      Italic,
      RemoveFormat,

      // Paragraph formatting (paragraph group)
      List,
      Indent,
      Alignment,

      // Links (links group)
      Link,

      // Insert (insert group)
      BlockQuote,
      Table,
      HorizontalLine,
      SpecialCharacters,
      Image,
      ImageBlock,
      ImageInline,
      ImageToolbar,
      //ImageUpload,
      ImageInsert,
      ImageInsertViaUrl,
      ImageTextAlternative,
      ImageCaption,
      CodeBlock,

      // Styles (styles group)
      FontSize,
      FontFamily,

      // Editing (editing group)
      FindAndReplace,
      Markdown,
    ],
    toolbar: [
      // Document/Mode
      'findAndReplace',
      '|',

      // Basic styles
      'heading',
      'fontSize',
      'fontFamily',
      '|',
      'bold',
      'italic',
      'removeFormat',
      '|',

      // Paragraph
      'alignment',
      'bulletedList',
      'numberedList',
      'outdent',
      'indent',
      '|',

      // Links and insertion
      'link',
      'insertImageViaUrl',
      //'uploadImage',
      'insertTable',
      'blockQuote',
      'codeBlock',
      'horizontalLine',
      'specialCharacters',
      '|',

      // Clipboard/Undo
      'undo',
      'redo',
    ],
    // Image configuration (pure Markdown - no resize/alignment)
    image: {
      toolbar: [
        'imageTextAlternative', // Alternative text (alt)
      ],
      insert: {
        integrations: ['url'],
      },
    },
    placeholder: placeholder,
  };

  // Use the config object directly (JavaScript doesn't need type casting)
  return ClassicEditor.create(element, config).then(editor => {
    editorInstance = editor;

    // Create character counter if maxLength is set
    if (maxCharLimit > 0) {
      createCharCounter(element);
    }

    // Listen for content changes
    editor.model.document.on('change:data', () => {
      const content = editor.getData();

      // Update character counter
      if (maxCharLimit > 0) {
        updateCharCounter();
      }

      // Call the onChange callback if provided
      if (onChangeCallback) {
        onChangeCallback(content);
      }
    });

    // Auto-fill alt text for images inserted without one
    editor.model.document.on('change:data', () => {
      const changes = editor.model.document.differ.getChanges();

      for (const change of changes) {
        if (change.type === 'insert' && change.name === 'imageBlock') {
          const position = change.position;
          const imageElement = position.nodeAfter;

          if (imageElement && imageElement.is('element', 'imageBlock')) {
            const src = imageElement.getAttribute('src');
            const alt = imageElement.getAttribute('alt');

            // If no alt text or default "Image", extract filename from URL
            if (src && (!alt || alt === 'Image')) {
              const filename = extractFilenameFromUrl(src);
              if (filename) {
                editor.model.change(writer => {
                  writer.setAttribute('alt', filename, imageElement);
                });
              }
            }
          }
        }
      }
    });

    return editor;
  });
}

export function getEditorContent() {
  if (editorInstance) {
    // Return pure Markdown content (no image attributes)
    return editorInstance.getData();
  }
  return '';
}

export function setEditorContent(content) {
  if (editorInstance) {
    // Set pure Markdown content directly
    editorInstance.setData(content || '');

    // Update character counter after setting content
    if (maxCharLimit > 0) {
      updateCharCounter();
    }
  }
}

export function destroyEditor() {
  if (editorInstance) {
    return editorInstance
      .destroy()
      .then(() => {
        editorInstance = null;
        // Clean up character counter
        if (charCounterElement) {
          charCounterElement.remove();
          charCounterElement = null;
        }
        maxCharLimit = 0;
        console.log('CKEditor destroyed');
      })
      .catch(error => {
        console.error('Error destroying CKEditor:', error);
      });
  }
  return Promise.resolve();
}

export function focusEditor() {
  if (editorInstance) {
    editorInstance.focus();
  }
}

/**
 * Check if content exceeds the max length limit
 * @returns {boolean} true if over limit, false otherwise
 */
export function isOverLimit() {
  if (maxCharLimit <= 0) return false;
  return getCharCount() > maxCharLimit;
}

export function insertImageIntoEditor(imageUrl, altText = '') {
  if (!editorInstance || !imageUrl) return;

  // Keep the URL as-is (relative or absolute)
  // Security check: allow relative paths starting with / or http(s) URLs
  const isRelativePath = imageUrl.startsWith('/');
  const isHttpUrl = /^https?:\/\//i.test(imageUrl);

  if (!isRelativePath && !isHttpUrl) {
    console.warn(
      'Blocked invalid URL (must be relative /path or http(s)):',
      imageUrl
    );
    return;
  }

  const finalImageUrl = imageUrl;

  //Insert image directly using model API - most reliable method
  try {
    editorInstance.model.change(writer => {
      // Create the image element with proper attributes
      const imageElement = writer.createElement('imageBlock', {
        src: finalImageUrl,
        alt: altText || 'Image',
      });

      // Insert at current cursor position
      const insertPosition =
        editorInstance.model.document.selection.getFirstPosition();
      editorInstance.model.insertContent(imageElement, insertPosition);
    });
  } catch (e) {
    console.error('Direct model insertion failed, trying command method:', e);

    // Fallback: Try the insertImage command
    try {
      editorInstance.execute('insertImage', {
        source: finalImageUrl,
      });
    } catch (commandError) {
      console.error(
        'insertImage command failed, trying HTML insertion:',
        commandError
      );

      // Final fallback: Insert as HTML and convert to model
      try {
        const imageHtml = `<img src="${finalImageUrl}" alt="${altText || 'Image'}" />`;
        const viewFragment = editorInstance.data.processor.toView(imageHtml);
        const modelFragment = editorInstance.data.toModel(viewFragment);

        editorInstance.model.change(() => {
          const insertPosition =
            editorInstance.model.document.selection.getFirstPosition();
          editorInstance.model.insertContent(modelFragment, insertPosition);
        });
      } catch (finalError) {
        console.error('All image insertion methods failed:', finalError);
      }
    }
  }
}

// Function to insert links into CKEditor
export function insertLinkIntoEditor(linkUrl, linkText = '', insertId = null) {
  if (!editorInstance || !linkUrl) {
    console.warn('Cannot insert link: missing editor instance or URL');
    return;
  }

  // Create unique insert ID if not provided
  const uniqueInsertId =
    insertId ||
    `link_${Date.now()}_${Math.random().toString(36).substring(2, 11)}`;

  // Check if this insert has already been processed
  if (processedInserts.has(uniqueInsertId)) {
    console.log('Link insert already processed, skipping:', uniqueInsertId);
    return;
  }

  // Mark as processed
  processedInserts.add(uniqueInsertId);

  console.log(
    'insertLinkIntoEditor called with:',
    linkUrl,
    linkText,
    'insertId:',
    uniqueInsertId
  );

  // Keep the URL as-is (relative or absolute)
  // Security check: allow relative paths starting with / or http(s) URLs
  const isRelativePath = linkUrl.startsWith('/');
  const isHttpUrl = /^https?:\/\//i.test(linkUrl);

  if (!isRelativePath && !isHttpUrl) {
    console.warn(
      'Blocked invalid URL (must be relative /path or http(s)):',
      linkUrl
    );
    return;
  }

  const fullLinkUrl = linkUrl;

  try {
    // Get current selection or insert position
    const selection = editorInstance.model.document.selection;
    const position = selection.getFirstPosition();

    // Use the link command which is more reliable
    editorInstance.model.change(writer => {
      // Create text element with link
      const linkElement = writer.createText(linkText || fullLinkUrl, {
        linkHref: fullLinkUrl,
      });

      // Insert the link at current position
      editorInstance.model.insertContent(linkElement, position);
    });

    console.log('Link inserted successfully');
  } catch (error) {
    console.error('Failed to insert link:', error);

    // Fallback: try using the link command
    try {
      // First insert the text
      const textToInsert = linkText || fullLinkUrl;
      editorInstance.model.change(writer => {
        const textElement = writer.createText(textToInsert);
        const position =
          editorInstance.model.document.selection.getFirstPosition();
        editorInstance.model.insertContent(textElement, position);

        // Select the inserted text
        const range = writer.createRange(
          position,
          position.getShiftedBy(textToInsert.length)
        );
        writer.setSelection(range);
      });

      // Then apply the link
      editorInstance.execute('link', fullLinkUrl);
      console.log('Link inserted using fallback method');
    } catch (fallbackError) {
      console.error('Fallback link insertion also failed:', fallbackError);
    }
  }
}

// Global functions to be called from other scripts
window.insertImageIntoEditor = insertImageIntoEditor;
window.insertLinkIntoEditor = insertLinkIntoEditor;

// Listen for messages from resources page
window.addEventListener('message', function (event) {
  console.log('Received postMessage:', event.data);
  if (event.data && event.data.type === 'insertImage') {
    insertImageIntoEditor(event.data.url, event.data.alt);
  } else if (event.data && event.data.type === 'insertLink') {
    insertLinkIntoEditor(event.data.url, event.data.text, event.data.insertId);
  }
});

// Listen for localStorage changes (for cross-tab communication)
window.addEventListener('storage', function (event) {
  if (event.key === 'pendingImageInsert' && event.newValue) {
    console.log('Storage event received for image:', event.newValue);
    try {
      const imageData = JSON.parse(event.newValue);
      if (imageData && imageData.type === 'insertImage') {
        console.log('Inserting image from localStorage:', imageData);
        insertImageIntoEditor(imageData.url, imageData.alt);
        // Clear the localStorage item after use
        localStorage.removeItem('pendingImageInsert');
      }
    } catch (error) {
      console.error('Error parsing storage event:', error);
    }
  } else if (event.key === 'pendingLinkInsert' && event.newValue) {
    console.log('Storage event received for link:', event.newValue);
    try {
      const linkData = JSON.parse(event.newValue);
      if (linkData && linkData.type === 'insertLink') {
        console.log('Inserting link from localStorage:', linkData);
        insertLinkIntoEditor(linkData.url, linkData.text, linkData.insertId);
        // Clear the localStorage item after use
        localStorage.removeItem('pendingLinkInsert');
      }
    } catch (error) {
      console.error('Error parsing link storage event:', error);
    }
  }
});

// Also check for pending insertions on page load/focus
document.addEventListener('DOMContentLoaded', function () {
  checkForPendingInserts();
});

window.addEventListener('focus', function () {
  checkForPendingInserts();
});

function checkForPendingInserts() {
  // Check for pending images
  const pendingImageData = localStorage.getItem('pendingImageInsert');
  if (pendingImageData) {
    console.log('Found pending image insert on focus/load:', pendingImageData);
    try {
      const imageData = JSON.parse(pendingImageData);
      if (imageData && imageData.type === 'insertImage') {
        // Check if the data is recent (within 30 seconds)
        if (Date.now() - imageData.timestamp < 30000) {
          console.log('Inserting pending image:', imageData);
          insertImageIntoEditor(imageData.url, imageData.alt);
        }
        localStorage.removeItem('pendingImageInsert');
      }
    } catch (error) {
      console.error('Error parsing pending image data:', error);
    }
  }

  // Check for pending links
  const pendingLinkData = localStorage.getItem('pendingLinkInsert');
  if (pendingLinkData) {
    console.log('Found pending link insert on focus/load:', pendingLinkData);
    try {
      const linkData = JSON.parse(pendingLinkData);
      if (linkData && linkData.type === 'insertLink') {
        // Check if the data is recent (within 30 seconds)
        if (Date.now() - linkData.timestamp < 30000) {
          console.log('Inserting pending link:', linkData);
          insertLinkIntoEditor(linkData.url, linkData.text, linkData.insertId);
        }
        localStorage.removeItem('pendingLinkInsert');
      }
    } catch (error) {
      console.error('Error parsing pending link data:', error);
    }
  }
}
