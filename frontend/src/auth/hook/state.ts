/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { OAUTH_STATE_KEY } from "./constants";
import { AuthTokenPayload } from "./useOAuth2";

// --- Gestion du state : permet de vérifier que la réponse de l'authentification provient bien de l'authentification

export type State<TData = AuthTokenPayload> = TData | null;

// https://medium.com/@dazcyril/generating-cryptographic-random-state-in-javascript-in-the-browser-c538b3daae50
export const generateState = () => {
   const validChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
   let array = new Uint8Array(40) as any;
   window.crypto.getRandomValues(array);
   array = array.map((x: number) => validChars.codePointAt(x % validChars.length));
   return String.fromCharCode.apply(null, array);
};
export const saveState = (state: string) => {
   sessionStorage.setItem(OAUTH_STATE_KEY, state);
};
export const removeState = () => {
   sessionStorage.removeItem(OAUTH_STATE_KEY);
};
