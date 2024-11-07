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
import { IDrawers, initialDrawers } from "../context/IDrawers";

export const DrawerReducer = (
   state = initialDrawers,
   action: AnyAction | undefined = undefined,
): IDrawers => {
   switch (action?.type) {
      case DRAWER_EVENEMENT:
         return {
            ...state,
            EVENEMENT: action.payload,
         };

      case DRAWER_UTILISATEUR:
         return {
            ...state,
            UTILISATEUR: action.payload?.utilisateur,
            UTILISATEUR_ROLE: action.payload?.role,
         };

      default:
         return state;
   }
};
