/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

// jest-dom adds custom jest matchers for asserting on DOM nodes.
// allows you to do things like:
// expect(element).toHaveTextContent(/react/i)
// learn more: https://github.com/testing-library/jest-dom
import "@testing-library/jest-dom";
import "@testing-library/jest-dom/extend-expect";

import nodeCrypto from "crypto";

global.matchMedia =
   global.matchMedia ||
   function () {
      // noinspection JSDeprecatedSymbols
      return {
         addListener: jest.fn(),
         removeListener: jest.fn(),
      };
   };

Object.defineProperty(globalThis, "crypto", {
   value: {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      getRandomValues: (arr: any) => nodeCrypto.randomBytes(arr.length),
   },
});
