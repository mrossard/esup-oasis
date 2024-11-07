module.exports = {
   extends: [
      "airbnb-typescript",
      "plugin:@typescript-eslint/recommended",
      "plugin:prettier/recommended",
   ],
   plugins: [
      "react",
      "@typescript-eslint"
   ],
   env: {
      browser: true,
      es6: true,
      jest: true,
   },

   parser: "@typescript-eslint/parser",
   parserOptions: {
      ecmaFeatures: {
         jsx: true,
      },
      ecmaVersion: 2018,
      sourceType: "module",
      project: "./tsconfig.json",
   },
   rules: {
      "linebreak-style": "off",
      "react/jsx-curly-brace-presence": "error",
      "prefer-template": "error",
      "@typescript-eslint/ban-ts-comment": "off",

      "prettier/prettier": [
         "error",
         {
            endOfLine: "auto",
         },
      ],
      "import/no-extraneous-dependencies": ["error", {
         "devDependencies": false,
         "optionalDependencies": false,
         "peerDependencies": false,
      }],
   },
   overrides: [
      {
         files: ["*.ts", "*.tsx"], // Your TypeScript files extension
         parserOptions: {
            project: ["./tsconfig.json"], // Specify it only for TypeScript files
         },
      },
   ],
};
