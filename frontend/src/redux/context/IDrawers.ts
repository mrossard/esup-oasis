/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { RoleValues } from "../../lib/Utilisateur";

export interface IDrawers {
   INTERVENANT?: string;
   BENEFICIAIRE?: string;
   EVENEMENT?: string;
   UTILISATEUR?: string;
   UTILISATEUR_ROLE?: RoleValues;
}

export const initialDrawers: IDrawers = {
   INTERVENANT: undefined,
   BENEFICIAIRE: undefined,
   EVENEMENT: undefined,
   UTILISATEUR: undefined,
   UTILISATEUR_ROLE: undefined,
};
