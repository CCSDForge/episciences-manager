# News List Display

Documentation sur l'affichage de la liste des news et la gestion des langues.

## Structure de la liste

La liste des news est affichée dans `templates/news/journalNews.html.twig` avec une structure en colonnes :

```
┌──────────┬─────────┬─────────────────────────────┬────────────┬──────────┐
│  Status  │  Title  │  Content (preview + image)  │  Created   │  Actions │
├──────────┼─────────┼─────────────────────────────┼────────────┼──────────┤
│ ● Online │ Titre   │ [IMG] Texte tronqué...      │ 2026-05-19 │  ✏️  🗑️  │
│ ○ Offline│ Titre 2 │ Texte sans image...         │ 2026-05-18 │  ✏️  🗑️  │
└──────────┴─────────┴─────────────────────────────┴────────────┴──────────┘
```

### Colonnes

| Colonne | Classe CSS | Description |
|---------|------------|-------------|
| Status | `.news-status` | Badge "Online" (vert) ou "Offline" (gris) |
| Title | `.news-title-col` | Titre de la news |
| Content | `.news-content-col` | Aperçu du contenu (300 caractères max) + thumbnail |
| Created | `.news-creation-col` | Date de création (format Y-m-d) |
| Actions | `.news-actions` | Boutons Edit et Delete |

## Gestion des langues

### Affichage selon la langue de l'interface

Le contenu s'affiche **uniquement** dans la langue de l'interface (`app.request.locale`), **sans fallback** :

```twig
{# Titre - PAS de fallback #}
{{ news.title[app.request.locale]|default('') }}

{# Contenu - PAS de fallback #}
{% set rawContent = news.htmlContent[app.request.locale]|default('') %}
```

**Comportement :**
- Si la traduction existe dans la langue courante : affiche le contenu
- Si la traduction n'existe pas : affiche vide (pas de fallback vers defaultLanguage)

### Pourquoi pas de fallback ?

Cela permet à l'utilisateur de voir clairement quelles news ont du contenu dans chaque langue. Un affichage vide indique qu'une traduction est nécessaire.

### Données multilingues via attributs data-*

Chaque item de news stocke toutes les traductions dans des attributs `data-*` pour le JavaScript :

```html
<div class="news-item"
     data-news-id="{{ news.id }}"
     data-title-fr="{{ news.title['fr'] }}"
     data-title-en="{{ news.title['en'] }}"
     data-content-fr="{{ news.htmlContent['fr'] }}"
     data-content-en="{{ news.htmlContent['en'] }}"
     data-link-fr="{{ news.link['fr'] }}"
     data-link-en="{{ news.link['en'] }}"
     data-visibility="public|invisible">
```

Ces attributs sont lus par `news.js` quand on clique sur "Éditer" pour charger les traductions dans le formulaire.

## Aperçu du contenu

### Texte tronqué

Le contenu est tronqué à 300 caractères avec ellipse :

```twig
{{ contentText|length > 300 ? contentText|slice(0, 300) ~ '…' : contentText }}
```

### Thumbnail de la première image

Si le contenu contient une image, elle est extraite et affichée en miniature :

```twig
{% set firstImage = rawContent|extract_first_image %}
{% if firstImage %}
    <img src="{{ firstImage }}" alt="" class="news-thumbnail rounded">
{% endif %}
```

Styles CSS (dans `news.scss`) :
```scss
.news-thumbnail {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border: 1px solid $border-color;
}
```

## Flash Messages

Les messages flash (succès, erreur) s'affichent en bas à droite via le composant partagé :

```twig
{% include 'components/flash_messages.html.twig' %}
<div id="news-alerts"></div>
```

- `flash_messages.html.twig` : Messages Symfony (après save, delete, etc.)
- `#news-alerts` : Messages JavaScript (validation côté client)

Les styles sont définis dans `assets/styles/components/alerts.scss`.

## Pagination

La liste utilise KnpPaginator pour la pagination :

```twig
{% if newsList is defined %}
    <div class="d-flex justify-content-center mt-4">
        {{ knp_pagination_render(newsList, '@KnpPaginator/Pagination/bootstrap_v5_pagination.html.twig') }}
    </div>
{% endif %}
```

## Actions

### Éditer

Le bouton "Éditer" ouvre le formulaire en mode édition :

```html
<button class="btn-edit-news" data-edit-url="{{ path('app_news_edit', {code: code, id: news.id}) }}">
```

Le JavaScript `news.js` :
1. Lit les attributs `data-*` de l'item
2. Remplit le formulaire avec les traductions
3. Change l'action du formulaire vers l'URL d'édition
4. Ouvre le collapse du formulaire

### Supprimer

Le bouton "Supprimer" ouvre une modal de confirmation :

```html
<button class="btn-delete-news"
        data-news-id="{{ news.id }}"
        data-delete-url="{{ path('app_news_delete', {code: code, id: news.id}) }}"
        data-news-title="{{ news.title[defaultLanguage] }}">
```

## Fichiers concernés

| Fichier | Rôle |
|---------|------|
| `templates/news/journalNews.html.twig` | Template principal de la liste |
| `templates/news/_news_form.html.twig` | Formulaire de création/édition |
| `assets/scripts/pages/news.js` | Logique JavaScript |
| `assets/styles/pages/news.scss` | Styles CSS |
| `src/Controller/NewsController.php` | Contrôleur backend |

## Comparaison avec Pages

| Aspect | News | Pages |
|--------|------|-------|
| **Affichage** | Liste/tableau | Sidebar hiérarchique |
| **Fallback langue** | Non | Non (contenu), Oui (titre YAML) |
| **Source des données** | Attributs `data-*` | `window.pageConfig` JSON |
| **Pagination** | Oui (KnpPaginator) | Non |
| **Création** | Oui (mode create) | Non (pages définies en YAML) |
| **Suppression** | Oui | Non |