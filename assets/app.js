import './bootstrap.js';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

// Disable debug console methods in production/preprod environments
// Reads environment from data-env attribute set by Symfony in Twig template
const isProduction = document.body?.dataset?.env !== 'dev';
if (isProduction) {
    // Disable debug console methods but keep error and warn
    console.log = () => {};
    console.debug = () => {};
    console.info = () => {};
    // Keep console.error and console.warn for production debugging
}

console.log('Welcome to Webpack Encore with Bootstrap!🎉');
