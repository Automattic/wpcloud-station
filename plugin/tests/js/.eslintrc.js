module.exports = {
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
    'import/no-unresolved': 'off',
    'no-unused-vars': 'warn',
    'jsx-a11y/label-has-associated-control': 'off',
    'prettier/prettier': 'off',
  },
};
