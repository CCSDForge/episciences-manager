import { Markdown } from '@ckeditor/ckeditor5-markdown-gfm';
import {
  ClassicEditor,
  Essentials,
  Bold,
  Italic,
  Strikethrough,
  Code,
  Paragraph,
  Heading,
  List,
  TodoList,
  Link,
  BlockQuote,
  Table,
  Undo,
  Indent,
  // CKEditor 4 group equivalents
  FindAndReplace, // editing > find and replace
  RemoveFormat, // basic styles > remove format
  SpecialCharacters, // insert > special characters
  SpecialCharactersEssentials, // arrows, currency, mathematical, latin, text
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
  ImageResize, // image resizing
  ImageStyle, // image styles and borders
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
 * Get plain text character count from editor content
 */
function getCharCount() {
  if (!editorInstance) return 0;
  const content = editorInstance.getData();
  // Remove HTML tags and decode entities for accurate count
  const plainText = content.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
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
  charCounterElement.classList.remove('text-danger', 'text-warning', 'text-muted', 'fw-bold');
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
  const existingCounter = editorElement.parentNode?.querySelector('.char-counter');
  if (existingCounter) existingCounter.remove();

  // Create new counter
  charCounterElement = document.createElement('div');
  charCounterElement.className = 'char-counter text-muted small mt-1 text-end';
  editorElement.parentNode?.appendChild(charCounterElement);

  updateCharCounter();
}

export function initializeCKEditor(elementId, placeholder = '', onChange = null, maxLength = 0) {
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
      Strikethrough,
      Code,
      RemoveFormat,

      // Paragraph formatting (paragraph group)
      List,
      TodoList,
      Indent,

      // Links (links group)
      Link,

      // Insert (insert group)
      BlockQuote,
      Table,
      HorizontalLine,
      SpecialCharacters,
      SpecialCharactersEssentials,
      Image,
      ImageBlock,
      ImageInline,
      ImageToolbar,
      //ImageUpload,
      ImageInsert,
      ImageInsertViaUrl,
      ImageTextAlternative,
      ImageCaption,
      ImageResize,
      ImageStyle,
      CodeBlock,

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
      '|',
      'bold',
      'italic',
      'strikethrough',
      'code',
      'removeFormat',
      '|',

      // Paragraph
      'bulletedList',
      'numberedList',
      'todoList',
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
    heading: {
      options: [
        {
          model: 'paragraph',
          title: 'Paragraph',
          class: 'ck-heading_paragraph',
        },
        {
          model: 'heading2',
          view: 'h2',
          title: 'Heading 2',
          class: 'ck-heading_heading2',
        },
        {
          model: 'heading3',
          view: 'h3',
          title: 'Heading 3',
          class: 'ck-heading_heading3',
        },
        {
          model: 'heading4',
          view: 'h4',
          title: 'Heading 4',
          class: 'ck-heading_heading4',
        },
        {
          model: 'heading5',
          view: 'h5',
          title: 'Heading 5',
          class: 'ck-heading_heading5',
        },
        {
          model: 'heading6',
          view: 'h6',
          title: 'Heading 6',
          class: 'ck-heading_heading6',
        },
      ],
    },
    codeBlock: {
      languages: [{ language: 'plaintext', label: 'Plain text' }],
    },
    // Image configuration
    image: {
      toolbar: [
        'imageTextAlternative', // Alternative text (alt)
        '|',
        'imageStyle:alignLeft', // Left alignment
        'imageStyle:alignCenter', // Center alignment
        'imageStyle:alignRight', // Right alignment
        '|',
        'resizeImage', // Resize
      ],
      resizeOptions: [
        {
          name: 'resizeImage:original',
          value: null,
          label: 'Original size',
        },
        {
          name: 'resizeImage:25',
          value: '25',
          label: '25%',
        },
        {
          name: 'resizeImage:50',
          value: '50',
          label: '50%',
        },
        {
          name: 'resizeImage:75',
          value: '75',
          label: '75%',
        },
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

    const editableEl = editor.ui.view.editable.element;
    const updateMinHeight = () => {
      if (!editableEl) return;
      const h = Math.max(420, editableEl.scrollHeight || 0);
      editableEl.style.minHeight = h + 'px';
    };

    editor.editing.view.document.on('change', updateMinHeight);
    // Initial adjustment
    setTimeout(updateMinHeight, 0);

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

    return editor;
  });
}

export function getEditorContent() {
  console.log('=== getEditorContent called ===');
  console.log('Editor instance exists:', !!editorInstance);

  if (editorInstance) {
    const content = editorInstance.getData();
    console.log('Raw editor data:', content);
    console.log('Content length:', content?.length || 0);
    return content;
  }

  console.log('No editor instance, returning empty string');
  return '';
}

export function setEditorContent(content) {
  if (editorInstance) {
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

  //Normalize to absolute URL (handles /path, //host, relative paths)
  const toAbsoluteUrl = url => {
    try {
      // new URL resolves relative paths against window.location.href
      return new URL(url, window.location.href).href;
    } catch {
      return url;
    }
  };

  const fullImageUrl = toAbsoluteUrl(imageUrl);

  //Security check: allow only http(s) URLs
  if (!/^https?:\/\//i.test(fullImageUrl)) {
    console.warn('Blocked non-http(s) URL:', fullImageUrl);
    return;
  }

  //Insert image directly using model API - most reliable method
  try {
    editorInstance.model.change(writer => {
      // Create the image element with proper attributes
      const imageElement = writer.createElement('imageBlock', {
        src: fullImageUrl,
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
        source: fullImageUrl,
      });
    } catch (commandError) {
      console.error(
        'insertImage command failed, trying HTML insertion:',
        commandError
      );

      // Final fallback: Insert as HTML and convert to model
      try {
        const imageHtml = `<img src="${fullImageUrl}" alt="${altText || 'Image'}" />`;
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

  // Normalize to absolute URL
  const toAbsoluteUrl = url => {
    try {
      return new URL(url, window.location.href).href;
    } catch {
      return url;
    }
  };

  const fullLinkUrl = toAbsoluteUrl(linkUrl);

  // Security check: allow only http(s) URLs
  if (!/^https?:\/\//i.test(fullLinkUrl)) {
    console.warn('Blocked non-http(s) URL:', fullLinkUrl);
    return;
  }

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
