/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { useEffect, useState } from "react";
import { IComposante, IDemande, IEtatDemande, IProfil, ITypeDemande } from "../../api/ApiTypeHelpers";
import { useApi } from "../../context/api/ApiProvider";
import dayjs from "dayjs";
import { TableExportButton } from "../Buttons/TableExportButton";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { FiltreDemande } from "./DemandeTable";
import {
   PREFETCH_COMPOSANTES,
   PREFETCH_ETAT_DEMANDE,
   PREFETCH_PROFILS,
   PREFETCH_TYPES_DEMANDES,
} from "../../api/ApiPrefetchHelpers";

const headers = [
   { label: "Demandeur (nom)", key: "demandeur.nom" },
   { label: "Demandeur (prénom)", key: "demandeur.prenom" },
   { label: "Composantes", key: "composantes" },
   { label: "Formations", key: "formations" },
   { label: "Type de demande", key: "typeDemande" },
   { label: "Date de dépôt", key: "dateDepot" },
   { label: "Etat de la demande", key: "etat" },
   { label: "Profil attribué", key: "profilAttribue" },
];

function getDemandesExportData(
   demandes: IDemande[],
   composantes: IComposante[] | undefined,
   etats: IEtatDemande[] | undefined,
   typesDemandes: ITypeDemande[] | undefined,
   profils: IProfil[] | undefined,
) {
   return demandes.map((demande) => {
      return {
         key: demande["@id"],
         "demandeur.nom": demande.demandeur?.nom,
         "demandeur.prenom": demande.demandeur?.prenom,
         dateDepot: demande.dateDepot ? dayjs(demande.dateDepot).format("DD/MM/YYYY") : "",
         composantes: demande.demandeur?.inscriptions
            ?.map((inscription) => inscription.formation)
            ?.map((formation) => formation?.composante)
            ?.map((composante) => {
               if (!composante) return null;
               return composantes?.find((c) => c["@id"] === composante);
            })
            .map((composante) => {
               return composante?.libelle?.replaceAll("\"", "\"\"");
            })
            .join(", "),
         formations: demande.demandeur?.inscriptions
            ?.map((inscription) => inscription.formation?.libelle?.replaceAll("\"", "\"\""))
            .join(", "),
         typeDemande: typesDemandes
            ?.find((t) => t["@id"] === demande.typeDemande)
            ?.libelle?.replaceAll("\"", "\"\""),
         etat: etats?.find((e) => e["@id"] === demande.etat)?.libelle,
         profilAttribue: profils
            ?.find((p) => p["@id"] === demande.profilAttribue)
            ?.libelle?.replaceAll("\"", "\"\""),
      };
   });
}

export default function DemandeTableExport(props: { filtreDemande: FiltreDemande }) {
   const [exportSubmit, setExportSubmit] = useState(false);
   const [downloaded, setDownloaded] = useState(false);
   const [loading, setLoading] = useState(false);

   const { data: demandes, isFetching: isFetchingDemandes } = useApi().useGetCollectionPaginated({
      path: "/demandes",
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
      query: {
         ...props.filtreDemande,
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         format_simple: true,
      },
      enabled: exportSubmit,
   });

   const { data: composantes, isFetching: isFetchingComposantes } = useApi().useGetCollection({
      ...PREFETCH_COMPOSANTES,
      enabled: exportSubmit,
   });

   const { data: etats, isFetching: isFetchingEtats } = useApi().useGetCollection({
      ...PREFETCH_ETAT_DEMANDE,
      enabled: exportSubmit,
   });

   const { data: typesDemandes, isFetching: isFetchingTypesDemandes } = useApi().useGetCollection({
      ...PREFETCH_TYPES_DEMANDES,
      enabled: exportSubmit,
   });

   const { data: profils, isFetching: isFetchingProfils } = useApi().useGetCollection({
      ...PREFETCH_PROFILS,
      enabled: exportSubmit,
   });

   // gestion des pré-chargements nécessaires pour l'export
   useEffect(() => {
      if (
         composantes?.items &&
         etats?.items &&
         typesDemandes?.items &&
         demandes?.items &&
         profils?.items
      ) {
         setLoading(false);
      } else {
         setLoading(
            isFetchingComposantes ||
            isFetchingEtats ||
            isFetchingTypesDemandes ||
            isFetchingDemandes ||
            isFetchingProfils,
         );
      }
   }, [
      composantes?.items,
      isFetchingComposantes,
      exportSubmit,
      etats?.items,
      isFetchingEtats,
      typesDemandes?.items,
      isFetchingTypesDemandes,
      isFetchingDemandes,
      isFetchingProfils,
      demandes?.items,
      profils?.items,
   ]);

   return (
      <TableExportButton
         filename="demandes"
         getData={() =>
            getDemandesExportData(
               demandes?.items || [],
               composantes?.items,
               etats?.items,
               typesDemandes?.items,
               profils?.items,
            )
         }
         loading={loading}
         setLoading={setLoading}
         downloaded={downloaded}
         setDownloaded={setDownloaded}
         submitted={exportSubmit}
         setSubmitted={setExportSubmit}
         headers={headers}
      />
   );
}
