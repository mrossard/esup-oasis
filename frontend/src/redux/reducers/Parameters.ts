/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { AnyAction } from "redux";
import { PARAMETER, PARAMETERS } from "../ReduxConstants";
import { initialParameters, IParameters } from "../context/IParameters";

// eslint-disable-next-line @typescript-eslint/default-param-last
export const ParametersReducer = (
   state = initialParameters,
   action: AnyAction | undefined = undefined,
): IParameters => {
   switch (action?.type) {
      case PARAMETER:
         return {
            ...state,
            parameters: [
               ...state.parameters.filter((p) => p.name !== action.payload.name),
               action.payload,
            ],
         };

      case PARAMETERS:
         return {
            ...state,
            parameters: action.payload,
         };

      default:
         return state;
   }
};
