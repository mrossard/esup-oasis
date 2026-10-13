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
import {
  comparerInscriptions,
  getAnneeUniversitaire,
  grouperInscriptionsParComposante,
  separerInscriptions,
} from "./inscription";

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

describe("getAnneeUniversitaire", () => {
  it.each([
    ["2025-09-01T00:00:00+00:00", "2025-2026"],
    ["2025-08-15T12:00:00+00:00", "2025-2026"],
    ["2026-01-05T00:00:00+00:00", "2025-2026"],
    ["2026-06-30T00:00:00+00:00", "2025-2026"],
  ])("rattache %s à l'année %s", (debut, attendu) => {
    expect(getAnneeUniversitaire(inscription("/i/1", "/c/1", debut, false))).toBe(attendu);
  });
});

describe("separerInscriptions", () => {
  it("renvoie deux listes vides sans inscription", () => {
    expect(separerInscriptions(undefined, now)).toEqual({ enCours: [], passees: [] });
  });

  it("sépare les inscriptions en cours des passées, ces dernières groupées par année décroissante", () => {
    const enCours = inscription("/i/1", "/c/1", "2026-09-01T00:00:00+00:00", true);
    const p2025a = inscription("/i/2", "/c/1", "2025-09-01T00:00:00+00:00", false, "2026-08-31");
    const p2025b = inscription("/i/3", "/c/2", "2025-10-01T00:00:00+00:00", false, "2026-08-31");
    const p2024 = inscription("/i/4", "/c/2", "2024-09-01T00:00:00+00:00", false, "2025-08-31");

    const { enCours: groupes, passees } = separerInscriptions(
      [p2024, p2025a, enCours, p2025b],
      now,
    );

    expect(groupes).toEqual([{ composante: "/c/1", inscriptions: [enCours] }]);
    expect(passees).toEqual([
      { annee: "2025-2026", inscriptions: [p2025b, p2025a] },
      { annee: "2024-2025", inscriptions: [p2024] },
    ]);
  });
});
