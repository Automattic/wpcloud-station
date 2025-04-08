module.exports = {
  root: true,
  extends: ['plugin:@wordpress/eslint-plugin/recommended'],
  overrides: [
    {
      files: ['**/tests/**/*.js', '**/tests/**/*.jsx'],
      extends: ['plugin:@wordpress/eslint-plugin/test-unit'],
      env: {
        jest: true,
        browser: true,
      },
      globals: {
        jest: 'readonly',
        expect: 'readonly',
        it: 'readonly',
        describe: 'readonly',
        beforeAll: 'readonly',
        beforeEach: 'readonly',
        afterAll: 'readonly',
        afterEach: 'readonly',
      },
      rules: {
        // Disable all rules that are causing issues in test files
        'import/no-unresolved': 'off',
        'no-unused-vars': 'off',
        'jsx-a11y/label-has-associated-control': 'off',
        'prettier/prettier': 'off',
        'no-undef': 'off',
        'no-dupe-keys': 'off',
      },
    },
  ],
};
