/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { useEffect, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { TableExportButton } from "../Buttons/TableExportButton";
import { Evenement } from "../../lib/Evenement";
import dayjs from "dayjs";
import { PREFETCH_CAMPUS, PREFETCH_TYPES_EVENEMENTS } from "../../api/ApiPrefetchHelpers";
import { ICampus, ITypeEvenement, IUtilisateur } from "../../api/ApiTypeHelpers";

const headers = [
   { label: "Début", key: "debut" },
   { label: "Fin", key: "fin" },
   { label: "Catégorie d'évènement", key: "categorie" },
   { label: "Bénéficiaires", key: "beneficiaires" },
   { label: "Intervenant / Renfort", key: "intervenant" },
   { label: "Campus", key: "campus" },
   { label: "Transmis à la RH", key: "transmisRh" },
];

function getEvenementsData(
   evenements: Evenement[],
   typesEvenements: ITypeEvenement[] | undefined,
   beneficiaires: IUtilisateur[] | undefined,
   intervenants: IUtilisateur[] | undefined,
   campus: ICampus[] | undefined,
) {
   return evenements.map((evenement) => {
      const intervenant = intervenants?.find((i) => i["@id"] === evenement.intervenant);
      return {
         key: evenement["@id"],
         "@id": evenement["@id"],
         debut: evenement.debut ? dayjs(evenement.debut).format("DD/MM/YYYY HH:mm") : "",
         fin: evenement.debut ? dayjs(evenement.fin).format("DD/MM/YYYY HH:mm") : "",
         categorie: evenement.type
            ? typesEvenements
               ?.find((t) => t["@id"] === evenement.type)
               ?.libelle?.replaceAll("\"", "\"\"")
            : "",
         beneficiaires: evenement.beneficiaires
            ?.map((beneficiaire) => {
               return beneficiaires?.find((b) => b["@id"] === beneficiaire);
            })
            .map((beneficiaire) => {
               return `${beneficiaire?.nom?.toLocaleUpperCase()} ${beneficiaire?.prenom}`;
            })
            .join(", "),
         intervenant: intervenant
            ? `${intervenant?.nom?.toLocaleUpperCase()} ${intervenant?.prenom}`
            : "",
         campus: campus?.find((c) => c["@id"] === evenement.campus)?.libelle?.replaceAll("\"", "\"\""),
         transmisRh: evenement.isTransmisRH() ? "Oui" : "Non",
      };
   });
}

interface TableEvenementsExportProps {
   evenements: Evenement[];
}

export default function EvenementTableExport({ evenements }: TableEvenementsExportProps) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);
   const { data: beneficiaires, isFetching: isFetchingBeneficiaires } =
      useApi().useGetCollectionPaginated({
         path: "/beneficiaires",
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
   const { data: campus, isFetching: isFetchingCampus } = useApi().useGetCollection({
      ...PREFETCH_CAMPUS,
      enabled: exportSubmit,
   });
   const { data: typesEvenements, isFetching: isFetchingTypesEvenements } =
      useApi().useGetCollection({ ...PREFETCH_TYPES_EVENEMENTS, enabled: exportSubmit });

   useEffect(() => {
      if (beneficiaires?.items && intervenants?.items && campus?.items && typesEvenements?.items) {
         setLoading(false);
      } else {
         setLoading(
            isFetchingBeneficiaires ||
            isFetchingIntervenants ||
            isFetchingCampus ||
            isFetchingTypesEvenements,
         );
      }
   }, [
      beneficiaires?.items,
      campus?.items,
      intervenants?.items,
      isFetchingBeneficiaires,
      isFetchingCampus,
      isFetchingIntervenants,
      isFetchingTypesEvenements,
      typesEvenements?.items,
      exportSubmit,
   ]);

   useEffect(() => {
      setExportSubmit(false);
      setDownloaded(false);
   }, [evenements]);

   return (
      <TableExportButton
         loading={loading}
         setLoading={setLoading}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         getData={() =>
            getEvenementsData(
               evenements,
               typesEvenements?.items,
               beneficiaires?.items,
               intervenants?.items,
               campus?.items,
            )
         }
         downloaded={downloaded}
         setDownloaded={setDownloaded}
         headers={headers}
         filename="evenements"
      />
   );
}
