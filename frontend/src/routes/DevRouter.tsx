/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useMemo } from "react";
import { useAuth } from "../auth/AuthProvider";
import { Navigate, Route, Routes } from "react-router-dom";
import LoginPage from "../auth/LoginPage";
import OAuthPopup from "../auth/OAuthPopup";
import { Utilisateur } from "../lib/Utilisateur";
import AppLayout from "../controls/AppLayout/AppLayout";
import Rgpd from "./commun/Rgpd";
import MentionsLegales from "./commun/MentionsLegales";
import { DEV_ROUTES } from "./DevRoutes";

/**
 * Router utilisé en développement.
 *
 * @returns {ReactElement} The routes for the Router component.
 */
export default function DevRouter(): ReactElement {
   const auth = useAuth();

   const routes = useMemo<ReactElement | null>(() => {
      function routesByUser(utilisateur: Utilisateur | undefined) {
         return DEV_ROUTES.filter(
            (route) =>
               route.roles === null ||
               (utilisateur && route.roles.some((rr) => utilisateur.roles.some((ru) => rr === ru))),
         ).map((route) => <Route key={route.path} path={route.path} element={route.element} />);
      }

      if (!auth.user?.uid || auth.isExpired()) {
         return (
            <>
               <Route element={<OAuthPopup />} path="/callback" />
               <Route path="/login" element={<LoginPage />} />
               <Route path="/" element={<LoginPage />} />
               <Route element={<Rgpd />} path="/rgpd" />
               <Route element={<MentionsLegales />} path="/credits" />
               <Route path="*" element={<LoginPage />} />
            </>
         );
      }

      return (
         <Route element={<AppLayout />}>
            {routesByUser(auth.user)}
            <Route path="*" element={<Navigate to="/dashboard" />} />
         </Route>
      );
   }, [auth]);

   return (
      <Routes>
         {routes}
         <Route element={<Rgpd />} path="/rgpd" />
         <Route element={<MentionsLegales />} path="/credits" />
      </Routes>
   );
}
