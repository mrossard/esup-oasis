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

export interface GroupeAnnee {
  /** Année universitaire, ex. « 2025-2026 ». */
  annee: string;
  /** Inscriptions de l'année : la principale d'abord, puis la plus récente. */
  inscriptions: IInscription[];
}

export interface InscriptionsSeparees {
  /** Inscriptions en cours, regroupées par composante. */
  enCours: GroupeInscriptions[];
  /** Inscriptions terminées, regroupées par année universitaire (la plus récente d'abord). */
  passees: GroupeAnnee[];
}

/** Principale d'abord, puis la plus récente (`debut` décroissant). */
export function comparerInscriptions(i1: IInscription, i2: IInscription): number {
  const parPrincipale = Number(!!i2.principale) - Number(!!i1.principale);
  if (parPrincipale !== 0) return parPrincipale;
  return (i2.debut || "").localeCompare(i1.debut || "");
}

const MOIS_RENTREE_UNIVERSITAIRE = 7; // août (0 = janvier) : une inscription débutée avant appartient à l'année précédente

const estEnCours = (inscription: IInscription, now: Dayjs): boolean =>
  dayjs(inscription.fin).isAfter(now);

/** Année universitaire de l'inscription d'après sa date de début (ex. « 2025-2026 »). */
export function getAnneeUniversitaire(inscription: IInscription): string {
  const debut = dayjs(inscription.debut);
  const anneeDebut = debut.month() >= MOIS_RENTREE_UNIVERSITAIRE ? debut.year() : debut.year() - 1;
  return `${anneeDebut}-${anneeDebut + 1}`;
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
    .filter((inscription) => estEnCours(inscription, now))
    .sort(comparerInscriptions)
    .forEach((inscription) => {
      const composante = (inscription.formation?.composante as string | undefined) ?? "";
      groupes.set(composante, [...(groupes.get(composante) ?? []), inscription]);
    });

  return [...groupes].map(([composante, items]) => ({ composante, inscriptions: items }));
}

/**
 * Sépare les inscriptions en cours (regroupées par composante, cf. {@link grouperInscriptionsParComposante})
 * des inscriptions terminées (regroupées par année universitaire décroissante).
 */
export function separerInscriptions(
  inscriptions: IInscription[] = [],
  now: Dayjs = dayjs(),
): InscriptionsSeparees {
  const parAnnee = new Map<string, IInscription[]>();

  inscriptions
    .filter((inscription) => !estEnCours(inscription, now))
    .sort(comparerInscriptions)
    .forEach((inscription) => {
      const annee = getAnneeUniversitaire(inscription);
      parAnnee.set(annee, [...(parAnnee.get(annee) ?? []), inscription]);
    });

  const passees = [...parAnnee]
    .map(([annee, items]) => ({ annee, inscriptions: items }))
    .sort((g1, g2) => g2.annee.localeCompare(g1.annee));

  return { enCours: grouperInscriptionsParComposante(inscriptions, now), passees };
}
