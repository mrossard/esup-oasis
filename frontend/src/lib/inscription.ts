/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 *
 */

import dayjs, { Dayjs } from "dayjs";
import { IInscription } from "@api";

export interface GroupeInscriptions {
  /** IRI de la composante (vide si l'inscription n'en référence aucune). */
  composante: string;
  /** Inscriptions de la composante : la principale d'abord, puis la plus récente. */
  inscriptions: IInscription[];
}

/** Principale d'abord, puis la plus récente (`debut` décroissant). */
export function comparerInscriptions(i1: IInscription, i2: IInscription): number {
  const parPrincipale = Number(!!i2.principale) - Number(!!i1.principale);
  if (parPrincipale !== 0) return parPrincipale;
  return (i2.debut || "").localeCompare(i1.debut || "");
}

/**
 * Regroupe les inscriptions en cours par composante.
 *
 * Les groupes suivent l'ordre de leur première inscription une fois les inscriptions triées
 * (cf. {@link comparerInscriptions}) : le premier groupe contient donc l'inscription principale.
 */
export function grouperInscriptionsParComposante(
  inscriptions: IInscription[] = [],
  now: Dayjs = dayjs(),
): GroupeInscriptions[] {
  const groupes = new Map<string, IInscription[]>();

  inscriptions
    .filter((inscription) => dayjs(inscription.fin).isAfter(now))
    .sort(comparerInscriptions)
    .forEach((inscription) => {
      const composante = (inscription.formation?.composante as string | undefined) ?? "";
      groupes.set(composante, [...(groupes.get(composante) ?? []), inscription]);
    });

  return [...groupes].map(([composante, items]) => ({ composante, inscriptions: items }));
}
