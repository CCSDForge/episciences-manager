// Jest configuration for frontend tests
require('@testing-library/jest-dom');

// Mock necessary global functions
global.fetch = jest.fn();
global.URL = URL;

// Mock window.location
delete window.location;
window.location = {
    href: 'http://localhost',
    origin: 'http://localhost',
    pathname: '/',
    search: '',
    hash: ''
};

// Mock commonly used DOM elements
document.body.innerHTML = `
  <div id="language-dropdown-toggle"></div>
  <div id="language-dropdown-menu"></div>
`;

// Cleanup after each test
afterEach(() => {
    jest.clearAllMocks();
    document.body.innerHTML = '';
});