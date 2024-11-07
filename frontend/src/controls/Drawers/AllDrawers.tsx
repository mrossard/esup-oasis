/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement } from "react";
import "./AllDrawers.scss";
import EvenementDrawer from "./Evenement/EvenementDrawer";
import UtilisateurDrawer from "./Utilisateur/UtilisateurDrawer";

/**
 * Renders all the drawers of the application.
 *
 * @return {ReactElement} - The JSX element representing all the drawers.
 */
export default function AllDrawers(): ReactElement {
   return (
      <>
         <EvenementDrawer />
         <UtilisateurDrawer />
      </>
   );
}
