// Configuration Jest pour les tests frontend
require('@testing-library/jest-dom');

// Mock des fonctions globales nécessaires
global.fetch = jest.fn();
global.URL = URL;

// Mock de window.location
delete window.location;
window.location = {
    href: 'http://localhost',
    origin: 'http://localhost',
    pathname: '/',
    search: '',
    hash: ''
};

// Mock des éléments DOM couramment utilisés
document.body.innerHTML = `
  <div id="language-dropdown-toggle"></div>
  <div id="language-dropdown-menu"></div>
`;

// Cleanup après chaque test
afterEach(() => {
    jest.clearAllMocks();
    document.body.innerHTML = '';
});