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
  FontColor, // couleurs > couleur de police
  FontBackgroundColor, // couleurs > couleur de fond
  SpecialCharacters, // insertion > caractères spéciaux
  HorizontalLine, // insertion > ligne horizontale
  Image, // insertion > image
  ImageToolbar, // insertion > barre d'outils d'image
  //ImageUpload, // insertion > téléchargement d'image
  ImageInsert, // insertion > insérer image par URL
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
      ImageToolbar,
      //ImageUpload,
      ImageInsert,
      ImageResize,
      ImageStyle,
      CodeBlock,

      // Styles (groupe styles)
      FontSize,
      FontFamily,

      // Couleurs (groupe colors)
      FontColor,
      FontBackgroundColor,

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

        // Couleurs
        'fontColor',
        'fontBackgroundColor',
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
        'insertImage',
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
      console.log('CKEditor 5 initialisé avec succès');

      // Redimensionnement automatique
      const editingView = editor.editing.view;
      editingView.document.on('change', () => {
        const editable = editingView.document.getRoot();
        const height = Math.max(200, editable.scrollHeight);
        editor.ui.view.editable.element.style.minHeight = height + 'px';
      });

      return editor;
    })
    .catch(error => {
      console.error('Erreur lors de l\'initialisation de CKEditor:', error);
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
        console.log('CKEditor détruit');
      })
      .catch(error => {
        console.error('Erreur lors de la destruction de CKEditor:', error);
      });
  }
  return Promise.resolve();
}

export function focusEditor() {
  if (editorInstance) {
    editorInstance.focus();
  }
}
