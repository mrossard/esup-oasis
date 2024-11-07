/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import {
   IIntervenant,
   IInterventionForfait,
   IPeriode,
   ITypeEvenement,
} from "../../api/ApiTypeHelpers";
import { useEffect, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { TableExportButton } from "../Buttons/TableExportButton";
import { getLibellePeriode } from "../../utils/dates";
import { PREFETCH_TYPES_EVENEMENTS } from "../../api/ApiPrefetchHelpers";

const headers: {
   label: string;
   key: string;
}[] = [
   { label: "Période", key: "periode" },
   { label: "Intervenant", key: "intervenant" },
   { label: "Intervenant (numéro étudiant)", key: "numeroEtudiant" },
   { label: "Catégorie d'évènement", key: "categorie" },
   { label: "Durée (en heures)", key: "duree" },
];

function getInterventionsForfaitData(
   interventionsForfait: IInterventionForfait[],
   intervenants: IIntervenant[] | undefined,
   periodes: IPeriode[] | undefined,
   typesEvenements: ITypeEvenement[] | undefined,
) {
   return interventionsForfait.map((interventionForfait) => {
      const periode = periodes?.find((p) => p["@id"] === interventionForfait.periode);
      const intervenant = intervenants?.find((i) => i["@id"] === interventionForfait.intervenant);
      return {
         key: interventionForfait["@id"],
         "@id": interventionForfait["@id"],
         periode: periode ? getLibellePeriode(periode?.debut, periode?.fin) : "",
         intervenant: intervenant
            ? `${intervenant?.nom?.toLocaleUpperCase()} ${intervenant?.prenom}`
            : "",
         numeroEtudiant: intervenant?.numeroEtudiant,
         categorie: typesEvenements
            ?.find((t) => t["@id"] === interventionForfait.type)
            ?.libelle?.replaceAll('"', '""'),
         duree: interventionForfait.heures,
      };
   });
}

interface TableInterventionsForfaitExportProps {
   interventionsForfait: IInterventionForfait[];
}

export default function InterventionForfaitTableExport({
   interventionsForfait,
}: TableInterventionsForfaitExportProps) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);
   const { data: periodes, isFetching: isFetchingPeriodes } = useApi().useGetCollectionPaginated({
      path: "/periodes",
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
      page: 1,
      enabled: exportSubmit,
   });
   const { data: intervenants, isFetching: isFetchingIntervenants } =
      useApi().useGetCollectionPaginated({
         path: "/intervenants",
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         page: 1,
         enabled: exportSubmit,
      });
   const { data: typesEvenements, isFetching: isFetchingTypesEvenements } =
      useApi().useGetCollection({ ...PREFETCH_TYPES_EVENEMENTS, enabled: exportSubmit });

   useEffect(() => {
      if (periodes?.items && intervenants?.items && typesEvenements?.items) {
         setLoading(false);
      } else {
         setLoading(isFetchingPeriodes || isFetchingIntervenants || isFetchingTypesEvenements);
      }
   }, [
      periodes,
      intervenants,
      typesEvenements,
      isFetchingPeriodes,
      isFetchingIntervenants,
      isFetchingTypesEvenements,
      exportSubmit,
   ]);

   useEffect(() => {
      setExportSubmit(false);
      setDownloaded(false);
   }, [interventionsForfait]);

   return (
      <TableExportButton
         loading={loading}
         setLoading={setLoading}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         getData={() =>
            getInterventionsForfaitData(
               interventionsForfait,
               intervenants?.items,
               periodes?.items,
               typesEvenements?.items,
            )
         }
         downloaded={downloaded}
         setDownloaded={setDownloaded}
         headers={headers}
         filename="interventionsForfait"
      />
   );
}
