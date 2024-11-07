/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { Suspense } from "react";
import { BrowserRouter } from "react-router-dom";
import App from "./App";
import { Provider } from "react-redux";
import store from "./redux/Store";
import { AuthProvider } from "./auth/AuthProvider";
import Spinner from "./controls/Spinner/Spinner";

function AppAuthWrapper() {
   return (
      <AuthProvider onSuccess={() => {}}>
         <App />
      </AuthProvider>
   );
}

export default function AppWrapper() {
   return (
      <React.StrictMode>
         <Suspense fallback={<Spinner />}>
            <Provider store={store}>
               <BrowserRouter>
                  <AppAuthWrapper />
               </BrowserRouter>
            </Provider>
         </Suspense>
      </React.StrictMode>
   );
}
