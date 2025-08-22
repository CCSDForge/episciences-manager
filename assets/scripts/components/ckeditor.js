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
  Alignment, // paragraph > align
  FindAndReplace, // editing > find
  RemoveFormat, // basicstyles > cleanup
  FontSize, // styles
  FontFamily, // styles
  FontColor, // colors
  FontBackgroundColor, // colors
  SpecialCharacters, // insert
  HorizontalLine, // insert
  Image, // insert
  ImageToolbar, // insert
  ImageUpload, // insert
  CodeBlock, // insert
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
    licenseKey: 'GPL', //Free key for open source use
    plugins: [
      Essentials, // Core
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
      ImageToolbar,
      ImageUpload,
      CodeBlock,

      // Styles (styles group)
      FontSize,
      FontFamily,

      // Colors (colors group)
      FontColor,
      FontBackgroundColor,

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

        // Colors
        'fontColor',
        'fontBackgroundColor',
        '|',

        // Paragraph
        'alignment',
        'bulletedList',
        'numberedList',
        'outdent',
        'indent',
        '|',

        // Links & Insert
        'link',
        'uploadImage',
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

    // Configuration des images
    image: {
      toolbar: [
        'imageTextAlternative',
        '|',
        'imageStyle:alignLeft',
        'imageStyle:alignCenter',
        'imageStyle:alignRight',
      ],
    },

    // Configuration des couleurs
    fontColor: {
      colors: [
        {
          color: 'hsl(0, 0%, 0%)',
          label: 'Black',
        },
        {
          color: 'hsl(0, 0%, 30%)',
          label: 'Dim grey',
        },
        {
          color: 'hsl(0, 0%, 60%)',
          label: 'Grey',
        },
        {
          color: 'hsl(0, 0%, 90%)',
          label: 'Light grey',
        },
        {
          color: 'hsl(0, 0%, 100%)',
          label: 'White',
          hasBorder: true,
        },
        {
          color: 'hsl(0, 75%, 60%)',
          label: 'Red',
        },
        {
          color: 'hsl(30, 75%, 60%)',
          label: 'Orange',
        },
        {
          color: 'hsl(60, 75%, 60%)',
          label: 'Yellow',
        },
        {
          color: 'hsl(90, 75%, 60%)',
          label: 'Light green',
        },
        {
          color: 'hsl(120, 75%, 60%)',
          label: 'Green',
        },
        {
          color: 'hsl(150, 75%, 60%)',
          label: 'Aquamarine',
        },
        {
          color: 'hsl(180, 75%, 60%)',
          label: 'Turquoise',
        },
        {
          color: 'hsl(210, 75%, 60%)',
          label: 'Light blue',
        },
        {
          color: 'hsl(240, 75%, 60%)',
          label: 'Blue',
        },
        {
          color: 'hsl(270, 75%, 60%)',
          label: 'Purple',
        },
      ],
      columns: 5,
      documentColors: 10,
    },

    fontBackgroundColor: {
      colors: [
        {
          color: 'hsl(0, 0%, 0%)',
          label: 'Black',
        },
        {
          color: 'hsl(0, 0%, 30%)',
          label: 'Dim grey',
        },
        {
          color: 'hsl(0, 0%, 60%)',
          label: 'Grey',
        },
        {
          color: 'hsl(0, 0%, 90%)',
          label: 'Light grey',
        },
        {
          color: 'hsl(0, 0%, 100%)',
          label: 'White',
          hasBorder: true,
        },
        {
          color: 'hsl(0, 75%, 60%)',
          label: 'Red',
        },
        {
          color: 'hsl(30, 75%, 60%)',
          label: 'Orange',
        },
        {
          color: 'hsl(60, 75%, 60%)',
          label: 'Yellow',
        },
        {
          color: 'hsl(90, 75%, 60%)',
          label: 'Light green',
        },
        {
          color: 'hsl(120, 75%, 60%)',
          label: 'Green',
        },
        {
          color: 'hsl(150, 75%, 60%)',
          label: 'Aquamarine',
        },
        {
          color: 'hsl(180, 75%, 60%)',
          label: 'Turquoise',
        },
        {
          color: 'hsl(210, 75%, 60%)',
          label: 'Light blue',
        },
        {
          color: 'hsl(240, 75%, 60%)',
          label: 'Blue',
        },
        {
          color: 'hsl(270, 75%, 60%)',
          label: 'Purple',
        },
      ],
      columns: 5,
      documentColors: 10,
    },

    placeholder: placeholder,
  })
    .then(editor => {
      editorInstance = editor;
      console.log('CKEditor 5 initialized successfully');

      // Auto-resize
      const editingView = editor.editing.view;
      editingView.document.on('change', () => {
        const editable = editingView.document.getRoot();
        const height = Math.max(200, editable.scrollHeight);
        editor.ui.view.editable.element.style.minHeight = height + 'px';
      });

      return editor;
    })
    .catch(error => {
      console.error('Error initializing CKEditor:', error);
      throw error;
    });
}

export function getEditorContent() {
  return editorInstance ? editorInstance.getData() : '';
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
