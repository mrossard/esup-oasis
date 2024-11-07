/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { legacy_createStore as createStore, Store } from "redux";
import allReducers from "./reducers";
import { IDrawers } from "./context/IDrawers";
import { IModals } from "./context/IModals";
import { IAccessibilite } from "./context/IAccessibilite";
import { IAffichageFiltres } from "./context/IAffichageFiltres";
import { IParameters } from "./context/IParameters";

export interface IStore {
   drawers: IDrawers;
   modals: IModals;
   accessibilite: IAccessibilite;
   affichageFiltres: IAffichageFiltres;
   parameters: IParameters;
}

/**
 * Store redux avec les reducers context, drawers, modals, accessibilite, filtres et parameters
 * To do : remplacé par des providers
 * + Redux DevTools extension (DEV).
 *
 * @return {Store<IStore>} - The created Redux store.
 */
export const store = createStore(
   allReducers,
   // @ts-ignore
   window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__(),
);
store.getState();
export default store as Store<IStore>;
