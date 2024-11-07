/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { AnyAction } from "redux";
import {
   AFFICHAGE_AFFICHAGE,
   AFFICHAGE_AFFICHAGE_FILTRE,
   AFFICHAGE_FILTRE,
   SET_AFFICHAGE_FILTRE,
} from "../ReduxConstants";
import { IAffichageFiltres, initialAffichageFiltres } from "../context/IAffichageFiltres";

export const AffichageFiltresReducer = (
   // eslint-disable-next-line @typescript-eslint/default-param-last
   state = initialAffichageFiltres,
   action: AnyAction | undefined = undefined,
): IAffichageFiltres => {
   switch (action?.type) {
      case AFFICHAGE_AFFICHAGE:
         return {
            ...state,
            affichage: { ...state.affichage, ...action.payload },
         };

      case AFFICHAGE_FILTRE:
         return {
            ...state,
            filtres: { ...state.filtres, ...action.payload },
         };

      case AFFICHAGE_AFFICHAGE_FILTRE:
         return {
            ...state,
            affichage: { ...state.affichage, ...action.payload.affichage },
            filtres: { ...state.filtres, ...action.payload.filtres },
         };

      case SET_AFFICHAGE_FILTRE:
         return {
            affichage: { ...state.affichage },
            filtres: { ...action.payload.filtres },
         };

      default:
         return state;
   }
};
