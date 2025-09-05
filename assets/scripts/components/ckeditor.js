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
  ImageResize, // image resizing
  ImageStyle, // image styles and borders
  CodeBlock, // insert > code block
  // Note: SourceEditing is not available in this version
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

let editorInstance = null;

export function initializeCKEditor(elementId, placeholder = '') {
  const element = document.getElementById(elementId);
  if (!element) {
    console.error(`Element with ID ${elementId} not found`);
    return null;
  }

  return ClassicEditor.create(element, {
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
      ImageResize,
      ImageStyle,
      CodeBlock,

      // Styles (styles group)
      FontSize,
      FontFamily,

      // Editing (editing group)
      FindAndReplace,
    ],
    toolbar: {
      items: [
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
        'resizeImage', // Resizing
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
  }).then(editor => {
    editorInstance = editor;

    const editableEl = editor.ui.view.editable.element;
    const updateMinHeight = () => {
      if (!editableEl) return;
      const h = Math.max(200, editableEl.scrollHeight || 0);
      editableEl.style.minHeight = h + 'px';
    };

    editor.editing.view.document.on('change', updateMinHeight);
    // Initial adjustment
    setTimeout(updateMinHeight, 0);

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
  }
}

export function destroyEditor() {
  if (editorInstance) {
    return editorInstance
      .destroy()
      .then(() => {
        editorInstance = null;
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
export function insertImageIntoEditor(imageUrl, altText = '') {
  if (!editorInstance || !imageUrl) return;

  // 1) Normalize to absolute URL (handles /path, //host, relative paths)
  const toAbsoluteUrl = url => {
    try {
      // new URL resolves relative paths against window.location.href
      return new URL(url, window.location.href).href;
    } catch {
      return url;
    }
  };

  const fullImageUrl = toAbsoluteUrl(imageUrl);

  // 2) Security check: allow only http(s) URLs
  if (!/^https?:\/\//i.test(fullImageUrl)) {
    console.warn('Blocked non-http(s) URL:', fullImageUrl);
    return;
  }

  // 3) Insert image directly using model API - most reliable method
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
        commandError,
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

// Global function to be called from other scripts
window.insertImageIntoEditor = insertImageIntoEditor;

// Listen for messages from resources page
window.addEventListener('message', function (event) {
  if (event.data && event.data.type === 'insertImage') {
    insertImageIntoEditor(event.data.url, event.data.alt);
  }
});

// Listen for localStorage changes (for cross-tab communication)
window.addEventListener('storage', function (event) {
  if (event.key === 'pendingImageInsert' && event.newValue) {
    console.log('Storage event received:', event.newValue);
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
  }
});

// Also check for pending images on page load/focus
document.addEventListener('DOMContentLoaded', function () {
  checkForPendingImageInsert();
});

window.addEventListener('focus', function () {
  checkForPendingImageInsert();
});

function checkForPendingImageInsert() {
  const pendingData = localStorage.getItem('pendingImageInsert');
  if (pendingData) {
    console.log('Found pending image insert on focus/load:', pendingData);
    try {
      const imageData = JSON.parse(pendingData);
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
}
