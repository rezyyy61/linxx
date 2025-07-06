module.exports = {
    root: true,
    env: {
        browser: true,
        es2021: true,
    },
    parser: 'vue-eslint-parser',
    parserOptions: {
        parser: '@babel/eslint-parser',
        ecmaVersion: 2021,
        sourceType: 'module',
        requireConfigFile: false,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-recommended',
        '@vue/eslint-config-standard'
    ],
    plugins: ['vue'],
    rules: {
        'no-unused-vars': 'warn',
        'no-console': 'off',
        'vue/multi-word-component-names': 'off',
        'vue/no-v-html': 'off',
        'vue/require-default-prop': 'off',
        'vue/require-prop-types': 'off',

        // ⬇ Formatting-related (کاهش حساسیت)
        'vue/html-indent': 'off',
        'indent': 'off',
        'vue/max-attributes-per-line': 'off',
        'vue/singleline-html-element-content-newline': 'off',
        'vue/multiline-html-element-content-newline': 'off',
        'vue/html-self-closing': 'off',
        'vue/attributes-order': 'off',
        'semi': 'off',
        'quotes': 'off',
        'comma-dangle': 'off',
        'space-before-function-paren': 'off',
        'no-multiple-empty-lines': 'off',
        'object-curly-spacing': 'off',
    },
    globals: {
        defineProps: 'readonly',
        defineEmits: 'readonly',
        defineExpose: 'readonly',
        withDefaults: 'readonly',
    },
};
