/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { AnyAction } from "redux";
import { MODAL_EVENEMENT, MODAL_EVENEMENT_ID } from "../ReduxConstants";
import { IModals, initialModals } from "../context/IModals";

export const ModalReducer = (
   state = initialModals,
   action: AnyAction | undefined = undefined,
): IModals => {
   switch (action?.type) {
      case MODAL_EVENEMENT_ID:
         return {
            ...state,
            EVENEMENT_ID: action.payload,
         };

      case MODAL_EVENEMENT:
         return {
            ...state,
            EVENEMENT: action.payload,
         };

      default:
         return state;
   }
};
