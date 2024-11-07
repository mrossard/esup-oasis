/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { IBeneficiaire, IComposante, ITag, IUtilisateur } from "../../api/ApiTypeHelpers";
import { useEffect, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { TableExportButton } from "../Buttons/TableExportButton";
import { FiltreBeneficiaire } from "./BeneficiaireTable";
import { PREFETCH_COMPOSANTES, PREFETCH_TAGS } from "../../api/ApiPrefetchHelpers";
import { env } from "../../env";

const headers = [
   { label: "Nom", key: "nom" },
   { label: "Prénom", key: "prenom" },
   { label: "Email", key: "email" },
   { label: "Numéro étudiant", key: "numeroEtudiant" },
   { label: "Composantes", key: "composantes" },
   { label: "Formations", key: "formations" },
   { label: "Gestionnaires", key: "gestionnaires" },
   { label: "Tags", key: "tags" },
   { label: `Avis ${env.REACT_APP_ESPACE_SANTE_ABV || "santé"}`, key: "avisESE" },
];

function getBeneficiairesData(
   beneficiaires: IBeneficiaire[],
   composantes: IComposante[] | undefined,
   gestionnaires: IUtilisateur[] | undefined,
   tags: ITag[] | undefined,
) {
   return beneficiaires.map((beneficiaire) => {
      return {
         key: beneficiaire["@id"],
         "@id": beneficiaire["@id"],
         nom: beneficiaire.nom?.toLocaleUpperCase(),
         prenom: beneficiaire.prenom,
         email: beneficiaire.email,
         numeroEtudiant: beneficiaire.numeroEtudiant,
         composantes: beneficiaire.inscriptions
            ?.map((inscription) => inscription.formation)
            ?.map((formation) => formation?.composante)
            ?.map((composante) => {
               if (!composante) return null;
               return composantes?.find((c) => c["@id"] === composante);
            })
            .map((composante) => {
               return composante?.libelle?.replaceAll('"', '""');
            })
            .join(", "),
         formations: beneficiaire.inscriptions
            ?.map((inscription) => inscription.formation)
            ?.map((formation) => {
               if (!formation) return null;

               if (formation.codeExterne) {
                  return `[${formation.codeExterne}] ${formation.libelle?.replaceAll('"', '""')}`;
               }
               return `${formation.libelle?.replaceAll('"', '""')}`;
            })
            .join(", "),
         gestionnaires: beneficiaire.gestionnairesActifs
            ?.map((gestionnaire) => {
               return gestionnaires?.find((g) => g["@id"] === gestionnaire);
            })
            .map((gestionnaire) => {
               return `${gestionnaire?.nom?.toLocaleUpperCase()} ${gestionnaire?.prenom}`;
            })
            .join(", "),
         avisESE: beneficiaire.etatAvisEse,
         tags: beneficiaire.tags
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

interface TableBeneficiairesExportProps {
   filtreBeneficiaire: FiltreBeneficiaire;
}

export default function BeneficiaireTableExport({
   filtreBeneficiaire,
}: TableBeneficiairesExportProps) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);
   const { data: composantes, isFetching: isFetchingComposantes } = useApi().useGetCollection({
      ...PREFETCH_COMPOSANTES,
      enabled: exportSubmit,
   });
   const { data: gestionnaires, isFetching: isFetchingGestionnaires } =
      useApi().useGetCollectionPaginated({
         path: "/roles/{roleId}/utilisateurs",
         parameters: { roleId: "/roles/ROLE_PLANIFICATEUR" },
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         enabled: exportSubmit,
      });

   const { data: beneficiaires, isFetching: isFetchingBeneficiaires } =
      useApi().useGetCollectionPaginated({
         path: "/beneficiaires",
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         query: {
            ...filtreBeneficiaire,
            page: 1,
            itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         },
         enabled: exportSubmit,
      });

   const { data: tags, isFetching: isFetchingTags } = useApi().useGetCollection({
      ...PREFETCH_TAGS,
      enabled: exportSubmit,
   });

   useEffect(() => {
      if (composantes?.items && gestionnaires?.items && beneficiaires?.items && tags?.items) {
         setLoading(false);
      } else {
         setLoading(
            isFetchingComposantes ||
               isFetchingGestionnaires ||
               isFetchingBeneficiaires ||
               isFetchingTags,
         );
      }
   }, [
      composantes?.items,
      gestionnaires?.items,
      isFetchingComposantes,
      isFetchingGestionnaires,
      exportSubmit,
      isFetchingBeneficiaires,
      beneficiaires?.items,
      isFetchingTags,
      tags?.items,
   ]);

   return (
      <TableExportButton
         loading={loading}
         setLoading={setLoading}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         getData={() =>
            getBeneficiairesData(
               beneficiaires?.items || [],
               composantes?.items,
               gestionnaires?.items,
               tags?.items,
            )
         }
         downloaded={downloaded}
         setDownloaded={setDownloaded}
         headers={headers}
         filename="beneficiaires"
      />
   );
}
