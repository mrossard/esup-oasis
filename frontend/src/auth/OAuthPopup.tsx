/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

// OAuth2Popup.jsx
import React, { ReactElement, useEffect } from "react";
import { queryToObject } from "../utils/url";
import "./OAuthPopup.scss";
import Spinner from "../controls/Spinner/Spinner";

const OAUTH_STATE_KEY = "react-use-oauth2-state-key";
const OAUTH_RESPONSE = "react-use-oauth2-response";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const checkState = (receivedState: any) => {
   const state = sessionStorage.getItem(OAUTH_STATE_KEY);
   return state === receivedState;
};

/**
 * OAuthPopup component
 *
 * Gère les retours OAuth et renvoie la réponse à la fenêtre d'ouverture.
 *
 * @param {Object} props - The component props
 * @returns {ReactElement} - The component JSX element
 *
 * @throws {Error} - Throws an error if there is no window opener
 */
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const OAuthPopup = (props: any): ReactElement => {
   const {
      Component = (
         <div className="oauth-callback">
            <Spinner size={40} />
         </div>
      ),
   } = props;

   // On mount
   useEffect(() => {
      window.setTimeout(() => {
         const payload = queryToObject(window.location.hash.split("#")[1]);
         const state = payload && payload.state;
         const error = payload && payload.error;

         if (!window.opener) {
            throw new Error("No window opener");
         }
         debugger;
         if (error) {
            window.opener.postMessage({
               type: OAUTH_RESPONSE,
               error: decodeURI(error) || "OAuth error: An error has occured.",
            });
         } else if (state && checkState(state)) {
            window.opener.postMessage({
               type: OAUTH_RESPONSE,
               payload,
            });
         } else {
            window.opener.postMessage({
               type: OAUTH_RESPONSE,
               error: "OAuth error: State mismatch.",
            });
         }
      }, 0);
   }, []);

   return Component;
};

export default OAuthPopup;
