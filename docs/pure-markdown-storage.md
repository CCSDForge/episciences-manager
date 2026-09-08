# Pure Markdown Storage

## Objectif

Simplifier le stockage du contenu en sauvegardant du **Markdown pur** dans la base de données, sans les extensions d'attributs pour les images.

## Modifications effectuées

### 1. Backend PHP - `MarkdownService.php`

**Fichier:** `src/Service/MarkdownService.php`

**Changement:** Suppression de `AttributesExtension`

```php
// AVANT
use League\CommonMark\Extension\Attributes\AttributesExtension;
// ...
$environment->addExtension(new AttributesExtension());

// APRÈS
// AttributesExtension supprimée - plus d'import ni d'utilisation
```

**Impact:**
- Le Markdown avec attributs `![alt](url){width="50%" style="..."}` n'est plus interprété
- Les images s'affichent en taille originale
- Le contenu reste du Markdown standard compatible avec tous les parseurs

### 2. Frontend JavaScript - `ckeditor.js`

**Fichier:** `assets/scripts/components/ckeditor.js`

#### 2.1 Imports supprimés

```javascript
// SUPPRIMÉS
ImageResize,  // redimensionnement des images
ImageStyle,   // styles et alignement des images
```

#### 2.2 Plugins supprimés

```javascript
// SUPPRIMÉS de la liste des plugins
ImageResize,
ImageStyle,
```

#### 2.3 Configuration image simplifiée

```javascript
// AVANT
image: {
  toolbar: [
    'imageTextAlternative',
    '|',
    'imageStyle:alignLeft',
    'imageStyle:alignCenter',
    'imageStyle:alignRight',
    '|',
    'resizeImage',
  ],
  resizeOptions: [...],
  insert: { integrations: ['url'] },
}

// APRÈS
image: {
  toolbar: [
    'imageTextAlternative',  // Seul le texte alternatif reste
  ],
  insert: { integrations: ['url'] },
}
```

#### 2.4 Fonctions supprimées

| Fonction | Description |
|----------|-------------|
| `getImageAttributesInfo()` | Extraction des attributs d'images du modèle CKEditor |
| `imageStyleToCSS()` | Conversion style CKEditor → CSS |
| `addImageAttributesToMarkdown()` | Ajout des attributs au Markdown |
| `cssToImageStyle()` | Conversion CSS → style CKEditor |
| `parseImageAttributes()` | Extraction des attributs du Markdown |
| `applyImageAttributes()` | Application des attributs dans CKEditor |

#### 2.5 Fonctions simplifiées

**`getEditorContent()`**
```javascript
// AVANT - Ajoutait les attributs d'images au Markdown
export function getEditorContent() {
  const imageInfo = getImageAttributesInfo();
  const content = editorInstance.getData();
  return addImageAttributesToMarkdown(content, imageInfo);
}

// APRÈS - Retourne le Markdown pur
export function getEditorContent() {
  if (editorInstance) {
    return editorInstance.getData();
  }
  return '';
}
```

**`setEditorContent()`**
```javascript
// AVANT - Parsait et appliquait les attributs d'images
export function setEditorContent(content) {
  const { cleanMarkdown, attributesMap } = parseImageAttributes(content);
  // ... logique complexe pour appliquer les attributs
  editorInstance.setData(cleanMarkdown || '');
}

// APRÈS - Charge le Markdown directement
export function setEditorContent(content) {
  if (editorInstance) {
    editorInstance.setData(content || '');
    // ...
  }
}
```

## Format du Markdown stocké

### Avant (avec attributs)

```markdown
# Titre de la page

Voici une image redimensionnée:

![Photo](https://example.com/image.jpg){width="50%" style="float:right;margin-left:1em"}

Du texte qui entoure l'image...
```

### Après (Markdown pur)

```markdown
# Titre de la page

Voici une image:

![Photo](https://example.com/image.jpg)

Du texte après l'image...
```

## Compatibilité

### Contenu existant

Le contenu existant avec des attributs `{...}` sera:
- **Dans l'éditeur:** Les attributs seront affichés comme texte brut
- **À l'affichage:** Les attributs seront rendus comme texte (non interprétés)

**Recommandation:** Migrer le contenu existant pour supprimer les attributs `{...}`.

### Migration du contenu

Script de migration suggéré (à exécuter en SQL ou PHP):

```php
// Exemple de nettoyage des attributs d'images
$pattern = '/!\[([^\]]*)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)\{[^}]+\}/';
$replacement = '![$1]($2)';
$cleanMarkdown = preg_replace($pattern, $replacement, $markdown);
```

## Avantages du Markdown pur

1. **Portabilité** - Compatible avec tous les parseurs Markdown
2. **Simplicité** - Code plus simple et maintenable
3. **Prévisibilité** - Le contenu affiché correspond exactement au Markdown stocké
4. **Interopérabilité** - Export/import facilité vers d'autres systèmes

## Limitations

1. **Pas de redimensionnement** - Les images s'affichent en taille originale
2. **Pas d'alignement** - Les images sont toujours en bloc (pas de float)
3. **Styles via CSS** - Le styling doit être fait via CSS global si nécessaire

## Fichiers modifiés

| Fichier | Modification |
|---------|--------------|
| `src/Service/MarkdownService.php` | Suppression AttributesExtension |
| `assets/scripts/components/ckeditor.js` | Suppression gestion attributs images |