/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { combineReducers } from "redux";
import { DrawerReducer } from "./Drawers";
import { ModalReducer } from "./Modals";
import { AccessibiliteReducer } from "./Accessibilite";
import { AffichageFiltresReducer } from "./AffichageFiltres";
import { ParametersReducer } from "./Parameters";

const allReducers = combineReducers({
   drawers: DrawerReducer,
   modals: ModalReducer,
   accessibilite: AccessibiliteReducer,
   affichageFiltres: AffichageFiltresReducer,
   parameters: ParametersReducer,
});

export default allReducers;
