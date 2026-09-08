# Permissions des paramètres du journal

Ce document décrit le système de permissions pour l'accès aux paramètres du journal (frontend et backoffice).

## Vue d'ensemble

Les permissions sont gérées par le `ReviewVoter` (`src/Security/Voter/ReviewVoter.php`) qui vérifie les rôles de l'utilisateur pour un journal spécifique.

## Permissions disponibles

| Permission | Description | Rôles autorisés |
|-----------|-------------|-----------------|
| `REVIEW_VIEW` | Voir le dashboard et accéder aux pages de paramètres | `epiadmin`, `administrator`, `chief_editor`, `secretary` |
| `REVIEW_EDIT` | Modifier le contenu du journal (pages, news) | `epiadmin`, `administrator`, `chief_editor`, `secretary` |
| `REVIEW_EDIT_FRONTEND_SETTINGS` | Modifier les paramètres frontend | `epiadmin` uniquement |
| `REVIEW_EDIT_BACKOFFICE_SETTINGS` | Modifier les paramètres backoffice | `epiadmin`, `administrator` |

## Fonctionnement du voter

Le `ReviewVoter` vérifie deux conditions :
1. L'utilisateur possède un rôle autorisé
2. Ce rôle est associé au journal concerné (via `RVID`)

```php
// Exemple de vérification dans un contrôleur
$this->denyAccessUnlessGranted('REVIEW_EDIT_FRONTEND_SETTINGS', $review);
$this->denyAccessUnlessGranted('REVIEW_EDIT_BACKOFFICE_SETTINGS', $review);

// Ou pour une vérification conditionnelle
if ($this->isGranted('REVIEW_EDIT_FRONTEND_SETTINGS', $review)) {
    // Afficher les contrôles d'édition frontend
}
```

## Application aux pages de paramètres

### Page des paramètres frontend (`JournalFrontendSettingController`)

| Route | Action | Permission requise |
|-------|--------|-------------------|
| `/journal/{code}/frontend-settings` | Voir la page | `REVIEW_VIEW` |
| `/journal/{code}/frontend-settings/show` | API lecture | `REVIEW_VIEW` |
| `/journal/{code}/frontend-settings/edit` | API modification | `REVIEW_EDIT_FRONTEND_SETTINGS` |

### Page des paramètres backoffice (`JournalBackofficeSettingController`)

| Route | Action | Permission requise |
|-------|--------|-------------------|
| `/journal/{code}/backoffice-settings` | Voir la page | `REVIEW_VIEW` |
| `/journal/{code}/backoffice-settings/edit` | Formulaire édition | `REVIEW_EDIT_BACKOFFICE_SETTINGS` |
| `/journal/{code}/backoffice-settings/edit` (POST) | Sauvegarder | `REVIEW_EDIT_BACKOFFICE_SETTINGS` |

## Hiérarchie des rôles

| Rôle | Peut voir | Peut éditer contenu | Peut éditer frontend | Peut éditer backoffice |
|------|-----------|---------------------|----------------------|------------------------|
| `epiadmin` | ✅ | ✅ | ✅ | ✅ |
| `administrator` | ✅ | ✅ | ❌ | ✅ |
| `chief_editor` | ✅ | ✅ | ❌ | ❌ |
| `secretary` | ✅ | ✅ | ❌ | ❌ |
| Autres rôles | ❌ | ❌ | ❌ | ❌ |

## Modifier les rôles autorisés

Pour modifier les rôles autorisés, éditer les constantes dans `ReviewVoter` :

```php
// src/Security/Voter/ReviewVoter.php

private const VIEW_ROLES = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];
private const EDIT_ROLES = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];
private const EDIT_FRONTEND_SETTINGS_ROLES = ['epiadmin'];
private const EDIT_BACKOFFICE_SETTINGS_ROLES = ['epiadmin', 'administrator'];
```

## Sécurité

- Les routes de modification (`POST`/`PUT`) vérifient également un token CSRF
- Un utilisateur anonyme n'a jamais accès (vérifié en premier dans le voter)
- Le rôle doit être associé au journal spécifique, pas juste présent globalement