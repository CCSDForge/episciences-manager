// Jest configuration for frontend tests
require('@testing-library/jest-dom');

// Mock necessary global functions
global.fetch = jest.fn();
global.URL = URL;

// Mock window.location without triggering jsdom navigation
const mockLocation = {
  href: 'http://localhost',
  origin: 'http://localhost',
  pathname: '/',
  search: '',
  hash: '',
  assign: jest.fn(),
  replace: jest.fn(),
  reload: jest.fn(),
};

delete window.location;
window.location = mockLocation;

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
