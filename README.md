# Episciences Manager

An overlay journal management platform built with Symfony

![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue) ![Symfony](https://img.shields.io/badge/Symfony-7.2-green) ![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-yellow) [![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)

![CI Status](https://github.com/CCSDForge/episciences-manager/workflows/CI%20Pipeline/badge.svg) ![PHP Tests](https://img.shields.io/github/actions/workflow/status/CCSDForge/episciences-manager/ci.yml?label=PHP%20Tests&logo=php) ![JS Tests](https://img.shields.io/github/actions/workflow/status/CCSDForge/episciences-manager/ci.yml?label=JS%20Tests&logo=javascript) ![E2E Tests](https://img.shields.io/github/actions/workflow/status/CCSDForge/episciences-manager/ci.yml?label=E2E%20Tests&logo=playwright)

This repository hosts the management software for Episciences overlay journals platform. It's built with Symfony 7.2 and provides a modern web interface for journal administration and review management.

## Features

- **User Management**: Authentication with CAS integration
- **Journal Administration**: Tools for managing overlay journals
- **Markdown Support**: Built-in markdown processing for content
- **Modern Frontend**: Bootstrap 5 + Stimulus controllers
- **Multi-language**: French and English translations

More information about Episciences: https://www.episciences.org/

## Tech Stack

- **Backend**: PHP 8.2+ with Symfony 7.2
- **Database**: Mysql with Doctrine ORM
- **Frontend**: Bootstrap 5, Stimulus, Webpack Encore
- **Testing**: Pest (PHP) + Jest (JavaScript) + Playwright (E2E)
- **Authentication**: CAS (Central Authentication Service)

## Development Setup

```bash
# Install dependencies
composer install
npm install

# Build assets
npm run build

# Start development
symfony server:start
```

## Testing

```bash
# PHP tests
vendor/bin/pest

# JavaScript tests
npm test

# E2E tests
npm run test:e2e
```

## Credits
- The software is developed by the Center for the Direct Scientific Communication (CCSD).
- See [AUTHORS](AUTHORS) file

## License

This project is Free software (GPL v3) . See [LICENSE](LICENSE) for details.