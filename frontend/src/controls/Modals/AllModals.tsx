/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement } from "react";
import "./AllModals.scss";
import { IModals } from "../../redux/context/IModals";
import { useSelector } from "react-redux";
import { IStore } from "../../redux/Store";
import EvenementModal from "./Evenement/EvenementModal";
import { useAuth } from "../../auth/AuthProvider";
import EvenementResumeModal from "./Evenement/EvenementResumeModal";

/**
 * Renders app modals
 *
 * @returns {ReactElement|null} The rendered modal component.
 */
export default function AllModals(): ReactElement | null {
   const user = useAuth().user;
   const appModals: IModals = useSelector(({ modals }: Partial<IStore>) => modals) as IModals;

   if (user?.isPlanificateur) {
      return appModals.EVENEMENT || appModals.EVENEMENT_ID ? (
         <EvenementModal id={appModals.EVENEMENT_ID} initialEvenement={appModals.EVENEMENT} />
      ) : null;
   }

   return appModals.EVENEMENT_ID ? <EvenementResumeModal id={appModals.EVENEMENT_ID} /> : null;
}
