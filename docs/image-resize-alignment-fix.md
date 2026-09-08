# Correction du redimensionnement et alignement des images dans CKEditor

## Problème

Lors de l'édition des pages et news avec CKEditor, les utilisateurs pouvaient redimensionner les images (25%, 50%, 75%) et les aligner (gauche, centre, droite). Cependant, après la sauvegarde, ces modifications étaient perdues.

## Cause du problème

### Le format Markdown GFM

L'application utilise le plugin **Markdown GFM** (GitHub Flavored Markdown) de CKEditor. Ce plugin convertit automatiquement le contenu entre HTML et Markdown :

1. **Chargement** : Markdown → HTML (pour l'édition visuelle)
2. **Sauvegarde** : HTML → Markdown (pour le stockage)

### Limitation du Markdown standard

Le format Markdown standard pour les images est :

```markdown
![texte alternatif](url-image)
```

Cette syntaxe **ne supporte pas** :
- Les styles CSS (`width`, `float`, etc.)
- Les classes CSS
- Les attributs HTML personnalisés

### Ce qui se passait

1. L'utilisateur redimensionne une image à 50% dans CKEditor
2. CKEditor applique en interne : `<img style="width:50%;" src="..." />`
3. À la sauvegarde, le plugin Markdown convertit en : `![alt](url)`
4. **Les styles CSS sont perdus** car non supportés par Markdown

## Solution implémentée

### 1. Syntaxe Markdown étendue avec attributs

Nous utilisons une extension du Markdown qui supporte les attributs :

```markdown
![texte alternatif](url-image){width="50%" style="float:left;margin-right:1em"}
```

Cette syntaxe est supportée par plusieurs implémentations Markdown (CommonMark, Kramdown, etc.).

### 2. Modifications côté PHP (`MarkdownService.php`)

Ajout de l'extension `AttributesExtension` de league/commonmark :

```php
use League\CommonMark\Extension\Attributes\AttributesExtension;

$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new GithubFlavoredMarkdownExtension());
$environment->addExtension(new AttributesExtension());  // Nouvelle extension
```

Cette extension parse les attributs `{...}` et les convertit en attributs HTML :

- `{width="50%"}` → `<img width="50%" ... />`
- `{style="float:left"}` → `<img style="float:left" ... />`

### 3. Modifications côté JavaScript (`ckeditor.js`)

#### Sauvegarde (HTML → Markdown avec attributs)

La fonction `getEditorContent()` :

1. Parcourt le modèle CKEditor pour trouver les images
2. Récupère les attributs `resizedWidth` et `imageStyle`
3. Après conversion en Markdown, ajoute les attributs `{width="..." style="..."}`

```javascript
function getImageAttributesInfo() {
  // Récupère resizedWidth et imageStyle du modèle CKEditor
}

function addImageAttributesToMarkdown(markdown, imageInfo) {
  // Convertit ![alt](url) en ![alt](url){width="50%" style="..."}
}
```

#### Chargement (Markdown avec attributs → HTML)

La fonction `setEditorContent()` :

1. Parse les attributs `{...}` du Markdown
2. Enregistre un listener sur l'événement `change:data` de CKEditor
3. Charge le Markdown standard (sans attributs) dans CKEditor
4. Quand le contenu est chargé, le listener se déclenche
5. Utilise `requestAnimationFrame` pour attendre que le DOM soit prêt
6. Applique les attributs aux images via la commande `imageStyle`

```javascript
function parseImageAttributes(markdown) {
  // Extrait width et style des attributs {...}
}

function applyImageAttributes(attributesMap) {
  // 1. Applique resizedWidth directement via setAttribute
  // 2. Pour l'alignement : sélectionne chaque image puis exécute
  //    editor.execute('imageStyle', { value: 'alignLeft' })
}
```

**Note importante** : Pour l'alignement, on utilise `editor.execute('imageStyle', ...)` au lieu de `writer.setAttribute('imageStyle', ...)` car CKEditor ne réagit pas correctement à l'attribut défini directement.

### 4. Mapping des styles d'alignement

| CKEditor imageStyle | CSS généré |
|---------------------|------------|
| `alignLeft` | `float:left;margin-right:1em` |
| `alignRight` | `float:right;margin-left:1em` |
| `alignCenter` | `display:block;margin-left:auto;margin-right:auto` |

## Exemple de flux complet

### Sauvegarde

1. Image dans CKEditor avec `resizedWidth="50%"` et `imageStyle="alignLeft"`
2. CKEditor génère : `![mon image](https://example.com/photo.jpg)`
3. Notre code ajoute : `![mon image](https://example.com/photo.jpg){width="50%" style="float:left;margin-right:1em"}`
4. Stocké en base de données

### Affichage

1. Markdown en base : `![mon image](https://example.com/photo.jpg){width="50%" style="float:left;margin-right:1em"}`
2. MarkdownService convertit en : `<img width="50%" style="float:left;margin-right:1em" src="https://example.com/photo.jpg" alt="mon image" />`
3. Affiché avec la bonne taille et alignement

### Rechargement dans l'éditeur

1. Markdown chargé : `![mon image](https://example.com/photo.jpg){width="50%" style="float:left;margin-right:1em"}`
2. Attributs extraits : `{ width: "50%", style: "float:left;margin-right:1em" }`
3. CKEditor reçoit : `![mon image](https://example.com/photo.jpg)`
4. Attributs appliqués au modèle : `resizedWidth="50%"`, `imageStyle="alignLeft"`
5. L'image apparaît redimensionnée et alignée

## Fichiers modifiés

| Fichier | Modification |
|---------|--------------|
| `src/Service/MarkdownService.php` | Ajout de `AttributesExtension` |
| `assets/scripts/components/ckeditor.js` | Gestion des attributs d'image (sauvegarde et chargement) |

## Références

- [Attributes Extension - CommonMark for PHP](https://commonmark.thephpleague.com/2.6/extensions/attributes/)
- [GitHub Discussion - Image dimensions in CommonMark](https://github.com/thephpleague/commonmark/discussions/760)
- [GitLab - Change image dimensions in markdown](https://gitlab.com/gitlab-org/gitlab/-/issues/28118)
- [CKEditor 5 - Image Resize](https://ckeditor.com/docs/ckeditor5/latest/features/images/images-resizing.html)
- [CKEditor 5 - Image Styles](https://ckeditor.com/docs/ckeditor5/latest/features/images/images-styles.html)
- [CommonMark Spec](https://spec.commonmark.org/)

## Synchronisation du chargement

Pour appliquer les attributs au bon moment lors du chargement, on utilise le système d'événements de CKEditor plutôt qu'un `setTimeout` arbitraire :

```javascript
// 1. Enregistre un listener AVANT setData()
editorInstance.model.document.on('change:data', onDataChange);

// 2. setData() charge le contenu et déclenche l'événement
editorInstance.setData(cleanMarkdown);

// 3. Dans le listener, requestAnimationFrame garantit que le DOM est prêt
requestAnimationFrame(() => {
  applyImageAttributes(attributesMap);
});
```

**Avantages de cette approche :**
- Pas de délai arbitraire (fonctionne sur toutes les machines)
- Réactif : se déclenche quand CKEditor a vraiment fini de charger
- Utilise le système d'événements natif de CKEditor

## Limitations connues

1. Si une même image (même URL) apparaît plusieurs fois avec des tailles différentes, seule la dernière taille sera appliquée (la Map utilise l'URL comme clé)
2. Les attributs personnalisés autres que `width` et `style` ne sont pas supportés