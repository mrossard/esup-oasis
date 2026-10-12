/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 *
 */

import { describe, expect, it } from "vitest";
import dayjs from "dayjs";
import { IInscription } from "@api";
import { comparerInscriptions, grouperInscriptionsParComposante } from "./inscription";

const now = dayjs("2026-10-12");

function inscription(
  id: string,
  composante: string,
  debut: string,
  principale: boolean,
  fin = "2027-08-31T00:00:00+00:00",
): IInscription {
  return {
    "@id": id,
    debut,
    fin,
    principale,
    formation: { composante },
  } as unknown as IInscription;
}

describe("comparerInscriptions", () => {
  it("place la principale avant une secondaire plus récente", () => {
    const secondaire = inscription("/i/1", "/c/1", "2026-10-01T00:00:00+00:00", false);
    const principale = inscription("/i/2", "/c/1", "2026-09-01T00:00:00+00:00", true);

    expect([secondaire, principale].sort(comparerInscriptions)).toEqual([principale, secondaire]);
  });

  it("départage deux inscriptions de même nature par date de début décroissante", () => {
    const ancienne = inscription("/i/1", "/c/1", "2026-09-01T00:00:00+00:00", false);
    const recente = inscription("/i/2", "/c/1", "2026-10-01T00:00:00+00:00", false);

    expect([ancienne, recente].sort(comparerInscriptions)).toEqual([recente, ancienne]);
  });
});

describe("grouperInscriptionsParComposante", () => {
  it("renvoie une liste vide sans inscription", () => {
    expect(grouperInscriptionsParComposante(undefined, now)).toEqual([]);
  });

  it("ignore les inscriptions terminées", () => {
    const terminee = inscription("/i/1", "/c/1", "2025-09-01T00:00:00+00:00", true, "2026-08-31");

    expect(grouperInscriptionsParComposante([terminee], now)).toEqual([]);
  });

  it("regroupe par composante et place en tête le groupe de la principale", () => {
    const a = inscription("/i/1", "/c/licence", "2026-09-01T00:00:00+00:00", false);
    const b = inscription("/i/2", "/c/medecine", "2026-09-01T00:00:00+00:00", true);
    const c = inscription("/i/3", "/c/licence", "2026-10-01T00:00:00+00:00", false);
    const d = inscription("/i/4", "/c/medecine", "2026-10-01T00:00:00+00:00", false);

    const groupes = grouperInscriptionsParComposante([a, b, c, d], now);

    expect(groupes.map((g) => g.composante)).toEqual(["/c/medecine", "/c/licence"]);
    expect(groupes[0].inscriptions).toEqual([b, d]);
    expect(groupes[1].inscriptions).toEqual([c, a]);
  });

  it("ne modifie pas le tableau d'origine", () => {
    const a = inscription("/i/1", "/c/1", "2026-09-01T00:00:00+00:00", false);
    const b = inscription("/i/2", "/c/1", "2026-09-01T00:00:00+00:00", true);
    const source = [a, b];

    grouperInscriptionsParComposante(source, now);

    expect(source).toEqual([a, b]);
  });
});
