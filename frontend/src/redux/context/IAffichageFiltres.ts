/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { firstFridayAfter, firstMondayBefore } from "../../utils/dates";
import { AffectationFilterValues } from "../../controls/Filters/Affectation/AffectationFilter";
import { AnnulationFilterValues } from "../../controls/Filters/Annulation/AnnulationFilter";
import dayjs from "dayjs";
import { RoleValues } from "../../lib/Utilisateur";
import { Evenement } from "../../lib/Evenement";

import { DateAsString } from "../../utils/string";

export interface IFiltresEvenements {
   debut: Date;
   fin: Date;
   "exists[intervenant]"?: AffectationFilterValues;
   "exists[dateAnnulation]"?: AnnulationFilterValues;
   "campus[]"?: string[];
   "utilisateurCreation[]"?: string[];
   intervenant?: string;
   beneficiaire?: string;
   intervenantBeneficiaire?: string;
   intervenantBeneficiaireRole?: RoleValues.ROLE_INTERVENANT | RoleValues.ROLE_BENEFICIAIRE;
   type?: string[];
}

export interface IFiltresEvenementsApi {
   "debut[after]"?: DateAsString;
   "fin[before]"?: DateAsString;
   "exists[intervenant]"?: boolean;
   "exists[dateAnnulation]"?: boolean;
   "campus[]"?: string[];
   "utilisateurCreation[]"?: string[];
   intervenant?: string;
   beneficiaires?: string;
   "type[]"?: string[];
}

export enum DensiteValues {
   compact = "Compact",
   normal = "Normal",
   large = "Large",
}

export type TypeAffichageValues = "day" | "week" | "work_week" | "month" | "agenda";
export type TypeAffichageCustomValues = TypeAffichageValues | "custom";

export interface IAffichage {
   layout: PlanningLayout;
   type: TypeAffichageCustomValues;
   densite: DensiteValues;
   fitToScreen: boolean;
}

export enum PlanningLayout {
   calendar = "calendar",
   table = "table",
}

export interface IAffichageFiltres {
   affichage: IAffichage;
   filtres: IFiltresEvenements;
}

export const initialAffichageFiltres: IAffichageFiltres = {
   affichage: {
      type: "work_week",
      densite: DensiteValues.normal,
      fitToScreen: false,
      layout: PlanningLayout.calendar,
   },
   filtres: {
      debut: firstMondayBefore(new Date()),
      fin: firstFridayAfter(new Date()),
      "exists[intervenant]": undefined,
      "exists[dateAnnulation]": AnnulationFilterValues.EnCours,
   },
};

export const filtreToApiOnBackend = (filtre: IFiltresEvenements): IFiltresEvenementsApi => {
   const filtreApi: IFiltresEvenementsApi = {
      "debut[after]": filtre.debut ? dayjs(filtre.debut).format("YYYY-MM-DD 00:00:00") : undefined,
      "fin[before]": filtre.fin ? dayjs(filtre.fin).format("YYYY-MM-DD 23:59:59") : undefined,
      "campus[]": filtre["campus[]"],
      "utilisateurCreation[]": filtre["utilisateurCreation[]"],
      intervenant: filtre.intervenant,
      beneficiaires: filtre.beneficiaire,
      "type[]": filtre.type,
   };

   // Intervenant / Bénéficiaire
   if (filtre.intervenantBeneficiaire) {
      if (filtre.intervenantBeneficiaireRole === RoleValues.ROLE_BENEFICIAIRE) {
         filtreApi.beneficiaires = filtre.intervenantBeneficiaire;
      } else if (filtre.intervenantBeneficiaireRole === RoleValues.ROLE_INTERVENANT) {
         filtreApi.intervenant = filtre.intervenantBeneficiaire;
      }
   }

   // Affecté ?
   if (filtre["exists[intervenant]"] === AffectationFilterValues.Affectes) {
      filtreApi["exists[intervenant]"] = true;
   } else if (filtre["exists[intervenant]"] === AffectationFilterValues.NonAffectes) {
      filtreApi["exists[intervenant]"] = false;
   } else {
      delete filtreApi["exists[intervenant]"];
   }

   // Annulé ?
   if (filtre["exists[dateAnnulation]"] === AnnulationFilterValues.EnCours) {
      filtreApi["exists[dateAnnulation]"] = false;
   } else if (filtre["exists[dateAnnulation]"] === AnnulationFilterValues.Annules) {
      filtreApi["exists[dateAnnulation]"] = true;
   } else {
      delete filtreApi["exists[dateAnnulation]"];
   }

   return filtreApi;
};

export const filtreToApi = (filtre: IFiltresEvenements): IFiltresEvenementsApi => {
   return {
      "debut[after]": filtre.debut ? dayjs(filtre.debut).format("YYYY-MM-DD 00:00:00") : undefined,
      "fin[before]": filtre.fin ? dayjs(filtre.fin).format("YYYY-MM-DD 23:59:59") : undefined,
   };
};

export const filtrerEvenements = (
   evenements: Evenement[],
   filtre: IFiltresEvenements,
): Evenement[] => {
   let res = evenements;

   // Intervenant / Bénéficiaire
   if (filtre.intervenantBeneficiaire) {
      if (filtre.intervenantBeneficiaireRole === RoleValues.ROLE_BENEFICIAIRE) {
         res = res.filter((e) => e.beneficiaires.some((b) => b === filtre.intervenantBeneficiaire));
      } else if (filtre.intervenantBeneficiaireRole === RoleValues.ROLE_INTERVENANT) {
         res = res.filter((e) => e.intervenant === filtre.intervenantBeneficiaire);
      }
   }

   // Affecté ?
   if (filtre["exists[intervenant]"] === AffectationFilterValues.Affectes) {
      res = res.filter((e) => e.intervenant);
   } else if (filtre["exists[intervenant]"] === AffectationFilterValues.NonAffectes) {
      res = res.filter((e) => !e.intervenant);
   }

   // Annulé ?
   if (filtre["exists[dateAnnulation]"] === AnnulationFilterValues.EnCours) {
      res = res.filter((e) => !e.dateAnnulation);
   } else if (filtre["exists[dateAnnulation]"] === AnnulationFilterValues.Annules) {
      res = res.filter((e) => e.dateAnnulation);
   }

   // Campus
   if (filtre["campus[]"] && filtre["campus[]"].length > 0) {
      res = res.filter((e) => filtre["campus[]"]?.includes(e.campus as string));
   }

   // Utilisateur de création
   if (filtre["utilisateurCreation[]"] && filtre["utilisateurCreation[]"].length > 0) {
      res = res.filter((e) =>
         filtre["utilisateurCreation[]"]?.includes(e.utilisateurCreation as string),
      );
   }

   // Intervenant
   if (filtre.intervenant) {
      res = res.filter((e) => e.intervenant === filtre.intervenant);
   }

   // Bénéficiaire
   if (filtre.beneficiaire) {
      res = res.filter((e) => e.beneficiaires.some((b) => b === filtre.beneficiaire));
   }

   // Type
   if (filtre.type) {
      res = res.filter((e) => filtre.type?.some((t) => t === e.type));
   }

   return res;
};
