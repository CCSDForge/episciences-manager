const js = require('@eslint/js');
const globals = require('globals');

module.exports = [
    js.configs.recommended,
    {
        languageOptions: {
            ecmaVersion: 2021, // or "latest"
            sourceType: 'module',
            globals: { ...globals.browser, ...globals.node, ...globals.jest },
        },
        rules: {
            // Quality rules
            'no-unused-vars': 'warn',
            'no-console': 'off',

            // Match Prettier
            semi: ['error', 'always'],
            quotes: ['error', 'single'],
            'comma-dangle': ['error', {
                arrays: 'always-multiline',
                objects: 'always-multiline',
                imports: 'always-multiline',
                exports: 'always-multiline',
                functions: 'always-multiline', // aligns with Prettier trailingComma: 'all'
            }],
            'arrow-parens': ['error', 'as-needed'],   // aligns with arrowParens: 'avoid'
            'quote-props': ['error', 'as-needed'],    // aligns with quoteProps: 'as-needed'

            // Turn off formatting-only rules (handled by Prettier)
            indent: 'off',
            'max-len': 'off',
            'object-curly-spacing': 'off',
            'space-before-function-paren': 'off',
            'linebreak-style': 'off',

            // Safe to keep
            'no-trailing-spaces': 'error',
            'eol-last': ['error', 'always'],
        },
    },
    {
        ignores: [
            'node_modules/**',
            'public/build/**',
            'vendor/**',
            'assets/vendor/**',
        ],
    },
];
