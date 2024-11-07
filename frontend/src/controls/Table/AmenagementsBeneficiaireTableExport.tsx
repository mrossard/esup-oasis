/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

/* eslint-disable @typescript-eslint/no-explicit-any */
import { IComposante, ITag, ITypeAmenagement, IUtilisateur } from "../../api/ApiTypeHelpers";
import { useEffect, useMemo, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { TableExportButton } from "../Buttons/TableExportButton";
import { FiltreAmenagement, filtreAmenagementToApi } from "./AmenagementTableLayout";
import { PREFETCH_COMPOSANTES, PREFETCH_TAGS } from "../../api/ApiPrefetchHelpers";
import {
   buildAmenagementsBenefDatasource,
   getTypesAmenagements,
   TypesDomainesAmenagements,
} from "./AmenagementsBeneficiaireTable";
import { DOMAINES_AMENAGEMENTS_INFOS } from "../../lib/amenagements";
import { ModeAffichageAmenagement } from "../../routes/gestionnaire/beneficiaires/Amenagements";
import { useAuth } from "../../auth/AuthProvider";
import { Utilisateur } from "../../lib/Utilisateur";
import { env } from "../../env";

function getHeader(
   typesAmenagementsUtilises: TypesDomainesAmenagements[],
   user: Utilisateur | undefined,
) {
   const headers = [
      { label: "Nom", key: "nom" },
      { label: "Prénom", key: "prenom" },
      { label: "Email", key: "email" },
      { label: "Numéro étudiant", key: "numeroEtudiant" },
      { label: "Composante", key: "composante" },
      { label: "Formation", key: "formation" },
      user?.isGestionnaire ? { label: "Tags", key: "tags" } : null,
      user?.isGestionnaire
         ? { label: `Avis ${env.REACT_APP_ESPACE_SANTE_ABV || "santé"}`, key: "avisESE" }
         : null,
   ];

   Object.keys(DOMAINES_AMENAGEMENTS_INFOS)
      .filter((domainKey) => {
         return user?.isGestionnaire || DOMAINES_AMENAGEMENTS_INFOS[domainKey].visibleReferent;
      })
      .forEach((domaineKey) => {
         const domaine = DOMAINES_AMENAGEMENTS_INFOS[domaineKey];
         headers.push({
            label: `Nombre ${domaine.libelleLongPluriel}`,
            key: `${domaineKey}_count`,
         });

         typesAmenagementsUtilises
            .filter((ta) => ta.domaine.id === domaine.id)
            .forEach((ta) => {
               headers.push({
                  label: ta.typeAmenagement.libelle.replaceAll('"', '""'),
                  key: ta.typeAmenagement["@id"] as string,
               });
               headers.push({
                  label: `${ta.typeAmenagement.libelle.replaceAll('"', '""')} - Commentaire`,
                  key: `${ta.typeAmenagement["@id"]}_commentaire`,
               });
            });
      });

   return headers.filter((h) => h) as { label: string; key: string }[];
}

function getAmenagementsBeneficiaireData(
   user: Utilisateur | undefined,
   amenagements: any[],
   typesAmenagementsUtilises: TypesDomainesAmenagements[],
   beneficiaires?: IUtilisateur[],
   composantes?: IComposante[],
   tags?: ITag[],
) {
   return amenagements.map((amenagement) => {
      const data: any = {
         key: amenagement.key,
         "@id": amenagement.key,
         nom: beneficiaires?.find((b) => b["@id"] === amenagement.key)?.nom?.toLocaleUpperCase(),
         prenom: beneficiaires?.find((b) => b["@id"] === amenagement.key)?.prenom,
         email: beneficiaires?.find((b) => b["@id"] === amenagement.key)?.email,
         numeroEtudiant: beneficiaires?.find((b) => b["@id"] === amenagement.key)?.numeroEtudiant,
         composante: composantes
            ?.find(
               (c) =>
                  c["@id"] ===
                  (
                     beneficiaires?.find((b) => b["@id"] === amenagement.key)?.inscriptions || []
                  ).sort((i1, i2) => i2.debut?.localeCompare(i1.debut || "") || 0)[0]?.formation
                     ?.composante,
            )
            ?.libelle?.replaceAll('"', '""'),
         formation: (beneficiaires?.find((b) => b["@id"] === amenagement.key)?.inscriptions || [])
            .sort((i1, i2) => i2.debut?.localeCompare(i1.debut || "") || 0)[0]
            ?.formation?.libelle?.replaceAll('"', '""'),
         tags:
            beneficiaires
               ?.find((b) => b["@id"] === amenagement.key)
               ?.tags?.map((tag) => tags?.find((t) => t["@id"] === tag))
               .map((tag) => tag?.libelle)
               .join(", ") || "",
         avisESE: beneficiaires?.find((b) => b["@id"] === amenagement.key)?.etatAvisEse,
      };

      typesAmenagementsUtilises.forEach((ta) => {
         const am = amenagement[ta.typeAmenagement["@id"] as string];
         if (am) {
            data[ta.typeAmenagement["@id"] as string] = "Oui";
            data[`${ta.typeAmenagement["@id"]}_commentaire`] = (am.commentaire || "").replaceAll(
               '"',
               '""',
            );
         }
      });

      Object.keys(DOMAINES_AMENAGEMENTS_INFOS).forEach((domaineKey) => {
         const domaine = DOMAINES_AMENAGEMENTS_INFOS[domaineKey];
         data[`${domaineKey}_count`] = typesAmenagementsUtilises.filter(
            (c) => c.domaine.id === domaine.id && amenagement[c.typeAmenagement["@id"] as string],
         ).length;
      });

      return data;
   });
}

interface TableAmenagementsExportProps {
   filtreAmenagement: FiltreAmenagement;
   typesAmenagements: ITypeAmenagement[];
}

export default function AmenagementsBeneficiaireTableExport({
   filtreAmenagement,
   typesAmenagements,
}: TableAmenagementsExportProps) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);
   const user = useAuth().user;

   const { data: amenagements, isFetching: isFetchingAmenagements } = useApi().useGetCollection({
      path: "/amenagements/utilisateurs",
      query: {
         ...filtreAmenagementToApi(filtreAmenagement, ModeAffichageAmenagement.ParBeneficiaire),
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
      },
      enabled: exportSubmit,
   });

   const { data: beneficiaires, isFetching: isFetchingBeneficiaires } =
      useApi().useGetCollectionPaginated({
         path: "/beneficiaires",
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
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

   // --- Prepare data

   const typesAmenagementsUtilises = useMemo(() => {
      return getTypesAmenagements(amenagements?.items || [], typesAmenagements).sort((a, b) =>
         a.typeAmenagement.libelle.localeCompare(b.typeAmenagement.libelle),
      );
   }, [amenagements?.items, typesAmenagements]);

   const data = useMemo(
      () => {
         return buildAmenagementsBenefDatasource(
            amenagements?.items || [],
            typesAmenagementsUtilises,
         );
      },
      // eslint-disable-next-line react-hooks/exhaustive-deps
      [amenagements?.items, typesAmenagementsUtilises],
   );

   // ---

   useEffect(() => {
      if (amenagements?.items && beneficiaires?.items && composantes?.items && tags?.items) {
         setLoading(false);
      } else {
         setLoading(
            isFetchingAmenagements ||
               isFetchingBeneficiaires ||
               isFetchingComposantes ||
               isFetchingTags,
         );
      }
   }, [
      exportSubmit,
      isFetchingAmenagements,
      amenagements?.items,
      beneficiaires?.items,
      composantes?.items,
      isFetchingBeneficiaires,
      isFetchingComposantes,
      tags?.items,
      isFetchingTags,
   ]);

   // --- Export

   const headers = getHeader(typesAmenagementsUtilises, user);

   return (
      <TableExportButton
         loading={loading}
         setLoading={setLoading}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         getData={() =>
            getAmenagementsBeneficiaireData(
               user,
               data || [],
               typesAmenagementsUtilises,
               beneficiaires?.items,
               composantes?.items,
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
