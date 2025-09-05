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
  // Équivalents des groupes CKEditor 4
  Alignment, // paragraphe > alignement
  FindAndReplace, // édition > rechercher
  RemoveFormat, // styles de base > nettoyer
  FontSize, // styles > taille de police
  FontFamily, // styles > famille de police
  SpecialCharacters, // insertion > caractères spéciaux
  HorizontalLine, // insertion > ligne horizontale
  Image, // insertion > image
  ImageBlock, // image en tant que bloc
  ImageInline, // image inline
  ImageToolbar, // insertion > barre d'outils d'image
  //ImageUpload, // insertion > téléchargement d'image
  ImageInsert, // insertion > insérer image par URL
  ImageInsertViaUrl, // insertion > insérer image par URL (nouveau plugin)
  ImageTextAlternative,
  ImageResize, // redimensionnement d'image
  ImageStyle, // styles et bordures d'image
  CodeBlock, // insertion > bloc de code
  // Note: SourceEditing n'est pas disponible dans cette version
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
    licenseKey: 'GPL', // Clé gratuite pour usage open source
    plugins: [
      Essentials, // Essentiels
      Paragraph,
      Heading,
      Undo,

      // Styles de base (groupe basicstyles)
      Bold,
      Italic,
      RemoveFormat,

      // Formatage de paragraphe (groupe paragraph)
      List,
      Indent,
      Alignment,

      // Liens (groupe links)
      Link,

      // Insertion (groupe insert)
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

      // Styles (groupe styles)
      FontSize,
      FontFamily,

      // Édition (groupe editing)
      FindAndReplace,
    ],
    toolbar: {
      items: [
        // Document/Mode
        'findAndReplace',
        '|',

        // Styles de base
        'heading',
        'fontSize',
        'fontFamily',
        '|',
        'bold',
        'italic',
        'removeFormat',
        '|',

        // Paragraphe
        'alignment',
        'bulletedList',
        'numberedList',
        'outdent',
        'indent',
        '|',

        // Liens et insertion
        'link',
        'insertImageViaUrl',
        //'uploadImage',
        'insertTable',
        'blockQuote',
        'codeBlock',
        'horizontalLine',
        'specialCharacters',
        '|',

        // Presse-papiers/Annuler
        'undo',
        'redo',
      ],
    },

    // Configuration des images
    image: {
      toolbar: [
        'imageTextAlternative', // Description alternative (alt)
        '|',
        'imageStyle:alignLeft', // Alignement gauche
        'imageStyle:alignCenter', // Alignement centre
        'imageStyle:alignRight', // Alignement droite
        '|',
        'resizeImage', // Redimensionnement
      ],
      resizeOptions: [
        {
          name: 'resizeImage:original',
          value: null,
          label: 'Taille originale',
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
    // Premier ajustement
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
