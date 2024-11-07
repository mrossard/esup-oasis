/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { AnyAction } from "redux";
import { DRAWER_EVENEMENT, DRAWER_UTILISATEUR } from "../ReduxConstants";
import { RoleValues } from "../../lib/Utilisateur";

export const setDrawerUtilisateur = (
   value:
      | {
      utilisateur: string | undefined;
      role?: RoleValues;
   }
      | undefined,
): AnyAction => {
   return {
      type: DRAWER_UTILISATEUR,
      payload: value,
   };
};

export const setDrawerEvenement = (value: string | undefined): AnyAction => {
   return {
      type: DRAWER_EVENEMENT,
      payload: value,
   };
};
