/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import {
   IAmenagement,
   ICategorieAmenagement,
   IComposante,
   ITag,
   ITypeAmenagement,
   ITypeSuiviAmenagement,
   IUtilisateur,
} from "../../api/ApiTypeHelpers";
import { useEffect, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { TableExportButton } from "../Buttons/TableExportButton";
import { FiltreAmenagement, filtreAmenagementToApi } from "./AmenagementTableLayout";
import { getDomaineAmenagement } from "../../lib/amenagements";
import dayjs from "dayjs";
import {
   PREFETCH_CATEGORIES_AMENAGEMENTS,
   PREFETCH_COMPOSANTES,
   PREFETCH_TAGS,
   PREFETCH_TYPES_AMENAGEMENTS,
   PREFETCH_TYPES_SUIVI_AMENAGEMENTS,
} from "../../api/ApiPrefetchHelpers";
import { RoleValues } from "../../lib/Utilisateur";
import { ModeAffichageAmenagement } from "../../routes/gestionnaire/beneficiaires/Amenagements";
import { env } from "../../env";

const headers = [
   { label: "Nom", key: "nom" },
   { label: "Prénom", key: "prenom" },
   { label: "Email", key: "email" },
   { label: "Numéro étudiant", key: "numeroEtudiant" },
   { label: "Composante", key: "composante" },
   { label: "Formation", key: "formation" },
   { label: "Domaine", key: "domaine" },
   { label: "Catégorie", key: "categorie" },
   { label: "Type", key: "type" },
   { label: "Semestre 1", key: "semestre1" },
   { label: "Semestre 2", key: "semestre2" },
   { label: "Début", key: "debut" },
   { label: "Fin", key: "fin" },
   { label: "Commentaire", key: "commentaire" },
   { label: "Suivi", key: "suivi" },
   { label: "Chargé•e d'accompagnement", key: "chargeAccompagnement" },
   { label: `Avis ${env.REACT_APP_ESPACE_SANTE_ABV || "santé"}`, key: "avisESE" },
   { label: "Tags", key: "tags" },
];

function getAmenagementsData(
   amenagements: IAmenagement[],
   categories: ICategorieAmenagement[] | undefined,
   types: ITypeAmenagement[] | undefined,
   beneficiaires: IUtilisateur[] | undefined,
   suivis: ITypeSuiviAmenagement[] | undefined,
   composantes: IComposante[] | undefined,
   cas: IUtilisateur[] | undefined,
   tags: ITag[] | undefined,
) {
   return amenagements.map((amenagement) => {
      return {
         key: amenagement["@id"],
         "@id": amenagement["@id"],
         nom: beneficiaires
            ?.find((b) => b["@id"] === amenagement.beneficiaire)
            ?.nom?.toLocaleUpperCase(),
         prenom: beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)?.prenom,
         email: beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)?.email,
         numeroEtudiant: beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)
            ?.numeroEtudiant,
         domaine:
            getDomaineAmenagement(
               types?.find((ta) => ta["@id"] === amenagement.typeAmenagement),
            )?.singulier.replaceAll('"', '""') || "Non renseigné",
         categorie: categories?.find(
            (c) =>
               c["@id"] ===
               types
                  ?.find((ta) => ta["@id"] === amenagement.typeAmenagement)
                  ?.categorie.replaceAll('"', '""'),
         )?.libelle,
         type: types
            ?.find((ta) => ta["@id"] === amenagement.typeAmenagement)
            ?.libelle.replaceAll('"', '""'),
         semestre1: amenagement.semestre1 ? "Oui" : "Non",
         semestre2: amenagement.semestre2 ? "Oui" : "Non",
         debut: amenagement.debut ? dayjs(amenagement.debut).format("DD/MM/YYYY") : undefined,
         fin: amenagement.fin ? dayjs(amenagement.fin).format("DD/MM/YYYY") : undefined,
         commentaire: amenagement.commentaire?.replaceAll('"', '""'),
         suivi: suivis?.find((s) => s["@id"] === amenagement.suivi)?.libelle?.replaceAll('"', '""'),
         composante: composantes
            ?.find(
               (c) =>
                  c["@id"] ===
                  (
                     beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)
                        ?.inscriptions || []
                  ).sort((i1, i2) => i2.debut?.localeCompare(i1.debut || "") || 0)[0]?.formation
                     ?.composante,
            )
            ?.libelle?.replaceAll('"', '""'),
         formation: (
            beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)?.inscriptions || []
         )
            .sort((i1, i2) => i2.debut?.localeCompare(i1.debut || "") || 0)[0]
            ?.formation?.libelle?.replaceAll('"', '""'),
         chargeAccompagnement: (
            beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)
               ?.gestionnairesActifs || []
         )
            .map((g) => {
               const ca = cas?.find((c) => c["@id"] === g);
               return ca ? `${ca.nom?.toLocaleUpperCase()} ${ca.prenom}` : "";
            })
            .join(", "),
         avisESE: beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)?.etatAvisEse,
         tags: (beneficiaires?.find((b) => b["@id"] === amenagement.beneficiaire)?.tags || [])
            ?.map((tag) => {
               return tags?.find((t) => t["@id"] === tag);
            })
            .map((tag) => {
               return tag?.libelle?.replaceAll('"', '""');
            })
            .join(", "),
      };
   });
}

interface TableAmenagementsExportProps {
   filtreAmenagement: FiltreAmenagement;
}

export default function AmenagementTableExport({
   filtreAmenagement,
}: TableAmenagementsExportProps) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);
   const { data: categories, isFetching: isFetchingCategories } = useApi().useGetCollection({
      ...PREFETCH_CATEGORIES_AMENAGEMENTS,
      enabled: exportSubmit,
   });
   const { data: types, isFetching: isFetchingTypes } = useApi().useGetCollection({
      ...PREFETCH_TYPES_AMENAGEMENTS,
      enabled: exportSubmit,
   });
   const { data: beneficiaires, isFetching: isFetchingBeneficiaires } =
      useApi().useGetCollectionPaginated({
         path: "/beneficiaires",
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         enabled: exportSubmit,
      });
   const { data: suivis, isFetching: isFetchingSuivis } = useApi().useGetCollection({
      ...PREFETCH_TYPES_SUIVI_AMENAGEMENTS,
      enabled: exportSubmit,
   });
   const { data: cas, isFetching: isFetchingCas } = useApi().useGetCollectionPaginated({
      path: "/roles/{roleId}/utilisateurs",
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
      parameters: { roleId: `/roles/${RoleValues.ROLE_GESTIONNAIRE}` },
      enabled: exportSubmit,
   });
   const { data: composantes, isFetching: isFetchingComposantes } = useApi().useGetCollection({
      ...PREFETCH_COMPOSANTES,
      enabled: exportSubmit,
   });
   const { data: tags, isFetching: isFetchingTags } = useApi().useGetCollection({
      ...PREFETCH_TAGS,
      enabled: exportSubmit,
   });

   const { data: amenagements, isFetching: isFetchingAmenagements } =
      useApi().useGetCollectionPaginated({
         path: "/amenagements",
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         query: {
            ...filtreAmenagementToApi(filtreAmenagement, ModeAffichageAmenagement.ParAmenagement),
            page: 1,
            itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         },
         enabled: exportSubmit,
      });

   useEffect(() => {
      if (
         categories?.items &&
         types?.items &&
         amenagements?.items &&
         beneficiaires?.items &&
         suivis?.items &&
         composantes?.items &&
         cas?.items &&
         tags?.items
      ) {
         setLoading(false);
      } else {
         setLoading(
            isFetchingCategories ||
               isFetchingTypes ||
               isFetchingAmenagements ||
               isFetchingBeneficiaires ||
               isFetchingSuivis ||
               isFetchingComposantes ||
               isFetchingTags ||
               isFetchingCas,
         );
      }
   }, [
      exportSubmit,
      isFetchingAmenagements,
      amenagements?.items,
      categories?.items,
      types?.items,
      isFetchingCategories,
      isFetchingTypes,
      beneficiaires?.items,
      isFetchingBeneficiaires,
      suivis?.items,
      isFetchingSuivis,
      composantes?.items,
      isFetchingComposantes,
      cas?.items,
      isFetchingCas,
      tags?.items,
      isFetchingTags,
   ]);

   return (
      <TableExportButton
         loading={loading}
         setLoading={setLoading}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         getData={() =>
            getAmenagementsData(
               amenagements?.items || [],
               categories?.items,
               types?.items,
               beneficiaires?.items,
               suivis?.items,
               composantes?.items,
               cas?.items,
               tags?.items,
            )
         }
         downloaded={downloaded}
         setDownloaded={setDownloaded}
         headers={headers}
         filename="amenagements"
      />
   );
}
