import prettier from 'eslint-config-prettier/flat';
import vue from 'eslint-plugin-vue';

import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
  vue.configs['flat/essential'],
  vueTsConfigs.recommended,
  prettier,
  {
    ignores : ['vendor', 'node_modules', 'public', 'bootstrap/ssr', 'tailwind.config.js', 'resources/js/components/ui/*'],
  },
  {
    rules : {
      'vue/multi-word-component-names'     : 'off',
      '@typescript-eslint/no-explicit-any' : 'off',
      "indent"                             : ["error", 2],
      "key-spacing"                        : [
        "error",
        {
          "singleLine" : { beforeColon: false, afterColon: true },
          "multiLine"  : { beforeColon: true, afterColon: true, align: "colon" }
        }
      ],
      "vue/max-attributes-per-line" : [
        "error",
        {
          "singleline" : { "max": 5 },
          "multiline"  : { "max": 1 }
        }
      ],
      "vue/html-self-closing" : [
        "error",
        {
          "html" : { "void": "always", "normal": "never", "component": "always" },
          "svg"  : "always",
          "math" : "always"
        }
      ],
      // Allow script blocks without explicit lang attribute (JS/TS inferred by tooling)
      "vue/block-lang"                              : "off",
      "vue/no-mutating-props"                       : "off",
      "vue/singleline-html-element-content-newline" : "off",
      "vue/multiline-html-element-content-newline"  : "off",
      "vue/first-attribute-linebreak"               : [
        "error",
        {
          "singleline" : "ignore",
          "multiline"  : "below"
        }
      ]
    },
  },
);
