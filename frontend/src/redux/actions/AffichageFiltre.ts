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
import { IAffichage, IFiltresEvenements } from "../context/IAffichageFiltres";

export const setAffichage = (value: Partial<IAffichage>): AnyAction => {
   return {
      type: AFFICHAGE_AFFICHAGE,
      payload: value,
   };
};

export const setFiltres = (value: Partial<IFiltresEvenements>, replace?: boolean): AnyAction => {
   if (replace) {
      return {
         type: SET_AFFICHAGE_FILTRE,
         payload: { filtres: value },
      };
   }

   return {
      type: AFFICHAGE_FILTRE,
      payload: value,
   };
};

export const restoreFiltres = (value: Partial<IFiltresEvenements>): AnyAction => {
   return {
      type: SET_AFFICHAGE_FILTRE,
      payload: {
         filtres: value,
      },
   };
};

export const setAffichageFiltres = (
   affichage: Partial<IAffichage>,
   filtres: Partial<IFiltresEvenements>,
): AnyAction => {
   return {
      type: AFFICHAGE_AFFICHAGE_FILTRE,
      payload: { affichage, filtres },
   };
};
