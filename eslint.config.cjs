const js = require('@eslint/js');
const globals = require('globals');

module.exports = [
    js.configs.recommended,
    {
        files: ['**/*.{js,cjs,mjs}'],
        languageOptions: {
            ecmaVersion: 2021,
            sourceType: 'module',
            globals: { ...globals.browser, ...globals.node, ...globals.jest },
        },
        rules: {
            // Quality
            'no-unused-vars': 'warn',
            'no-console': 'off',

            // Match Prettier
            semi: ['error', 'always'],
            quotes: ['error', 'single', { avoidEscape: true, allowTemplateLiterals: true }],
            'comma-dangle': ['error', {
                arrays: 'always-multiline',
                objects: 'always-multiline',
                imports: 'always-multiline',
                exports: 'always-multiline',
                functions: 'always-multiline',// ← match "all"
                //functions: 'never',
            }],
            'arrow-parens': ['error', 'as-needed'],
            'quote-props': ['error', 'as-needed'],

            // Let Prettier handle pure formatting
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
        files: ['tests/javascript/**/*.js'],
        languageOptions: { globals: { ...globals.jest } },
    },

    {
        ignores: [
            'node_modules/**',
            'public/build/**',
            'vendor/**',
            'assets/vendor/**',
            '**/*.json', // Ignore JSON files
        ],
    },
];
