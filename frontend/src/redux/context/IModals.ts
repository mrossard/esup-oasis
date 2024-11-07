/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { IEvenement } from "../../api/ApiTypeHelpers";

export interface IModals {
   INTERVENANT?: string;
   BENEFICIAIRE?: string;
   EVENEMENT_ID?: string;
   EVENEMENT?: IEvenement;
}

export const initialModals: IModals = {
   INTERVENANT: undefined,
   BENEFICIAIRE: undefined,
   EVENEMENT_ID: undefined,
   EVENEMENT: undefined,
};
