# Security: XSS Prevention Fixes

Documentation des corrections de sécurité XSS effectuées et des recommandations pour les améliorations futures.

## Résumé

Cette documentation couvre :
1. Les corrections effectuées (templates Twig et JavaScript)
2. Les bonnes pratiques d'échappement
3. Les recommandations pour la sanitization côté serveur

---

## 1. Corrections effectuées

### Templates Twig

#### journalNews.html.twig

| Ligne | Avant | Après | Risque corrigé |
|-------|-------|-------|----------------|
| 104 | `src="{{ firstImage }}"` | `src="{{ firstImage\|e('html_attr') }}"` | XSS via URL d'image |
| 129 | `data-news-title="{{ news.title... }}"` | `data-news-title="{{ news.title...\|e('html_attr') }}"` | XSS via attribut |
| 193 | `{{ acceptedLanguages\|json_encode\|raw }}` | `{{ acceptedLanguages\|json_encode\|raw }}` | (inchangé - voir note ci-dessous) |
| 194 | `'{{ defaultLanguage }}'` | `{{ defaultLanguage\|json_encode\|raw }}` | XSS via contexte JS |
| 198 | `'{{ 'news.validation...'|trans }}'` | `{{ 'news.validation...'|trans\|json_encode\|raw }}` | XSS via contexte JS |

#### _news_form.html.twig

| Ligne | Avant | Après | Risque corrigé |
|-------|-------|-------|----------------|
| 43 | `value="{{ news.title[lang] }}"` | `value="{{ news.title[lang]\|e('html_attr') }}"` | XSS via hidden input |
| 45 | `value="{{ news.content[lang] }}"` | `value="{{ news.content[lang]\|e('html_attr') }}"` | XSS via hidden input |
| 47 | `value="{{ news.link[lang] }}"` | `value="{{ news.link[lang]\|e('html_attr') }}"` | XSS via hidden input |
| 63 | `value="{{ news.title[defaultLanguage] }}"` | `value="{{ news.title[defaultLanguage]\|e('html_attr') }}"` | XSS via input |
| 87 | `value="{{ news.link[defaultLanguage] }}"` | `value="{{ news.link[defaultLanguage]\|e('html_attr') }}"` | XSS via input |

### JavaScript

#### news.js

| Ligne | Avant | Après | Risque corrigé |
|-------|-------|-------|----------------|
| 287 | `innerHTML = \`...\${createTitle}\`` | `innerHTML = \`...\${escapeHtml(createTitle)}\`` | XSS via innerHTML |
| 477 | `innerHTML = \`...\${editTitle}\`` | `innerHTML = \`...\${escapeHtml(editTitle)}\`` | XSS via innerHTML |

#### journalPages.js

| Ligne | Avant | Après | Risque corrigé |
|-------|-------|-------|----------------|
| - | (ajout fonction) | `function escapeHtml(text) {...}` | Helper de sécurité |
| 184 | `innerHTML = \`...\${noContentMsg}\`` | `innerHTML = \`...\${escapeHtml(noContentMsg)}\`` | XSS via innerHTML |

---

## 2. Bonnes pratiques d'échappement

### Twig - Filtres selon le contexte

| Contexte | Filtre | Exemple |
|----------|--------|---------|
| Contenu HTML | `e('html')` ou rien (défaut) | `<p>{{ var }}</p>` |
| Attribut HTML | `e('html_attr')` | `<div data-x="{{ var\|e('html_attr') }}">` |
| JavaScript (dans `<script>`) | `json_encode\|raw` | `var x = {{ var\|json_encode\|raw }};` |
| URL | `e('url')` | `<a href="?q={{ var\|e('url') }}">` |
| CSS | `e('css')` | `style="color: {{ var\|e('css') }}"` |

### Piège important : `|json_encode` dans les balises `<script>`

**Problème :** Par défaut, Twig échappe automatiquement en HTML. Si vous utilisez `|json_encode` sans `|raw` dans une balise `<script>`, les guillemets seront échappés en `&quot;`, ce qui **casse le JavaScript**.

```twig
{# INCORRECT - casse le JavaScript #}
<script>
  var config = {
    languages: {{ languages|json_encode }}
  };
</script>
{# Résultat : languages: [&quot;en&quot;, &quot;fr&quot;] - ERREUR JS! #}

{# CORRECT - JSON valide #}
<script>
  var config = {
    languages: {{ languages|json_encode|raw }}
  };
</script>
{# Résultat : languages: ["en", "fr"] - OK #}
```

**Pourquoi `|raw` est sûr ici ?**
- `|json_encode` produit du JSON valide qui échappe déjà les caractères dangereux (`"`, `\`, etc.)
- Dans un contexte `<script>`, on a besoin du JSON brut, pas du JSON échappé en HTML
- Le risque XSS est géré par `json_encode` lui-même qui échappe les `</script>` et autres

**Règle :** Dans une balise `<script>`, toujours utiliser `|json_encode|raw`

### JavaScript - Méthodes sûres

```javascript
// Fonction d'échappement HTML
function escapeHtml(text) {
  if (text === null || text === undefined) {
    return '';
  }
  const div = document.createElement('div');
  div.textContent = String(text);
  return div.innerHTML;
}

// Utiliser textContent au lieu de innerHTML quand possible
element.textContent = userInput;  // Sûr

// Utiliser setAttribute pour les attributs
element.setAttribute('data-title', userInput);  // Sûr

// Éviter innerHTML avec des données utilisateur
element.innerHTML = userInput;  // Dangereux!
element.innerHTML = escapeHtml(userInput);  // Sûr
```

---

## 3. Protection CSRF

### Vérifications effectuées

| Fichier | Token ID | Status |
|---------|----------|--------|
| `_news_form.html.twig` | `news-create`, `news-edit` | ✅ OK |
| `journalNews.html.twig` | `news-delete` | ✅ OK |
| `_page_form.html.twig` | `page-edit` | ✅ OK |
| `NewsController.php` | Validation des tokens | ✅ OK |
| `PageController.php` | Validation des tokens | ✅ OK |

---

## 4. Recommandations futures

### Sanitization HTML côté serveur (NON IMPLÉMENTÉ)

**Problème actuel :** Le contenu HTML (de CKEditor) est stocké en base de données sans sanitization. Un attaquant pourrait soumettre du HTML malveillant en contournant JavaScript.

**Fichiers concernés :**
- `src/Controller/PageController.php` (lignes 161-163)
- `src/Controller/NewsController.php` (lignes 174-176, 282-284)

**Solution recommandée :** Utiliser Symfony HtmlSanitizer

```php
// Installation
// composer require symfony/html-sanitizer

// Configuration dans config/packages/html_sanitizer.yaml
framework:
    html_sanitizer:
        sanitizers:
            app.content_sanitizer:
                allow_safe_elements: true
                allow_elements:
                    img: ['src', 'alt', 'title', 'width', 'height']
                    a: ['href', 'title', 'target']
                    table: ['class']
                    # ... autres éléments CKEditor

// Utilisation dans le contrôleur
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

public function __construct(
    private readonly HtmlSanitizerInterface $htmlSanitizer
) {}

// Dans la méthode de sauvegarde
$sanitizedContent = $this->htmlSanitizer->sanitize($data['content']);
$contents[$lang] = $sanitizedContent;
```

**Éléments HTML à autoriser (CKEditor) :**
- Formatage : `p`, `h1-h6`, `strong`, `em`, `u`, `s`, `blockquote`, `pre`, `code`
- Listes : `ul`, `ol`, `li`
- Tableaux : `table`, `thead`, `tbody`, `tr`, `th`, `td`, `figure`, `figcaption`
- Médias : `img` (src, alt, width, height), `a` (href, target)
- Structure : `div`, `span`, `br`, `hr`

**Attributs à bloquer :**
- Tous les event handlers : `onclick`, `onerror`, `onload`, `onmouseover`, etc.
- `javascript:` dans les URLs
- `style` avec `expression()` ou `url()`

---

## 5. Checklist de sécurité

### Avant chaque déploiement

- [ ] Vérifier que tous les attributs HTML utilisent `|e('html_attr')`
- [ ] Vérifier que les contextes JavaScript utilisent `|json_encode`
- [ ] Vérifier que les `innerHTML` utilisent `escapeHtml()` ou affichent du contenu contrôlé
- [ ] Vérifier que les tokens CSRF sont présents et validés
- [ ] (Futur) Vérifier que la sanitization HTML est active côté serveur

### Fichiers à auditer régulièrement

```
templates/
├── news/
│   ├── journalNews.html.twig
│   └── _news_form.html.twig
├── pages/
│   ├── journalPages.html.twig
│   └── _page_form.html.twig
└── components/
    └── flash_messages.html.twig

assets/scripts/
├── pages/
│   ├── news.js
│   └── journalPages.js
└── components/
    └── ckeditor.js
```

---

## 6. Références

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [Twig Escaping Documentation](https://twig.symfony.com/doc/3.x/filters/escape.html)
- [Symfony HtmlSanitizer](https://symfony.com/doc/current/html_sanitizer.html)
- [CKEditor Security](https://ckeditor.com/docs/ckeditor5/latest/features/html/general-html-support.html)