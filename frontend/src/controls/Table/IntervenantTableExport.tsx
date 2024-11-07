/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { ICampus, ICompetence, IIntervenant } from "../../api/ApiTypeHelpers";
import { useEffect, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { TableExportButton } from "../Buttons/TableExportButton";
import { FiltreIntervenant } from "./IntervenantTable";
import { PREFETCH_CAMPUS, PREFETCH_COMPETENCES } from "../../api/ApiPrefetchHelpers";

const headers: {
   label: string;
   key: string;
}[] = [
   { label: "Nom", key: "nom" },
   { label: "Prénom", key: "prenom" },
   { label: "Email", key: "email" },
   { label: "Numéro étudiant", key: "numeroEtudiant" },
   { label: "Compétences", key: "competences" },
   { label: "Campus", key: "campus" },
];

function getIntervenantsData(
   intervenants: IIntervenant[],
   competences: ICompetence[] | undefined,
   campus: ICampus[] | undefined,
) {
   return intervenants.map((intervenant) => {
      return {
         key: intervenant["@id"],
         "@id": intervenant["@id"],
         nom: intervenant.nom?.toLocaleUpperCase(),
         prenom: intervenant.prenom,
         email: intervenant.email,
         numeroEtudiant: intervenant.numeroEtudiant,
         competences: intervenant.competences
            ?.map((competence) => {
               if (!competence) return null;
               return competences?.find((c) => c["@id"] === competence);
            })
            .map((competence) => {
               return competence?.libelle?.replaceAll("\"", "\"\"");
            })
            .join(", "),
         campus: intervenant.campus
            ?.map((c) => {
               if (!c) return null;
               return campus?.find((ca) => ca["@id"] === c);
            })
            ?.map((c) => {
               return `${c?.libelle?.replaceAll("\"", "\"\"")}`;
            })
            .join(", "),
      };
   });
}

interface TableIntervenantsExportProps {
   filtreIntervenant: FiltreIntervenant;
}

export default function IntervenantTableExport({
                                                  filtreIntervenant,
                                               }: TableIntervenantsExportProps) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const { data: intervenants, isFetching: isFetchingIntervenants } =
      useApi().useGetCollectionPaginated({
         path: "/intervenants",
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         query: {
            ...filtreIntervenant,
            page: 1,
            itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
            intervenantArchive:
               filtreIntervenant.intervenantArchive === "undefined"
                  ? undefined
                  : filtreIntervenant.intervenantArchive,
         },
         enabled: exportSubmit,
      });

   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);
   const { data: competences, isFetching: isFetchingCompetences } = useApi().useGetCollection({
      ...PREFETCH_COMPETENCES,
      enabled: exportSubmit,
   });
   const { data: campus, isFetching: isFetchingCampus } = useApi().useGetCollection({
      ...PREFETCH_CAMPUS,
      enabled: exportSubmit,
   });

   useEffect(() => {
      if (competences?.items && campus?.items && intervenants?.items) {
         setLoading(false);
      } else {
         setLoading(isFetchingCompetences || isFetchingCampus || isFetchingIntervenants);
      }
   }, [
      isFetchingCompetences,
      isFetchingCampus,
      competences?.items,
      campus?.items,
      exportSubmit,
      intervenants?.items,
      isFetchingIntervenants,
   ]);

   useEffect(() => {
      setExportSubmit(false);
      setDownloaded(false);
   }, [intervenants]);

   return (
      <TableExportButton
         loading={loading}
         setLoading={setLoading}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         getData={() =>
            getIntervenantsData(intervenants?.items || [], competences?.items, campus?.items)
         }
         downloaded={downloaded}
         setDownloaded={setDownloaded}
         headers={headers}
         filename="intervenants"
      />
   );
}
