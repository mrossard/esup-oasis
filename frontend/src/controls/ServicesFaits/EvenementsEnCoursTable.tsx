/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement } from "react";
import { useAuth } from "../../auth/AuthProvider";
import { useDispatch } from "react-redux";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import Spinner from "../Spinner/Spinner";
import { Table } from "antd";
import { Evenement } from "../../lib/Evenement";
import { evenementTableColumns } from "../Table/EvenementTableColumns";
import { setModalEvenementId } from "../../redux/actions/Modals";

/**
 * Returns a table component that displays ongoing events.
 *
 * @returns {ReactElement} The table component displaying the ongoing events.
 */
export function EvenementsEnCoursTable(): ReactElement {
   const user = useAuth().user;
   const dispatch = useDispatch();
   const { data: evenementsEnCours, isLoading } = useApi().useGetCollectionPaginated({
      path: "/evenements",
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
      query: {
         intervenant: user?.["@id"],
         "exists[periodePriseEnCompteRH]": false,
         "exists[dateAnnulation]": false,
      },
      enabled: !!user?.["@id"],
   });

   if (!evenementsEnCours) {
      return <Spinner />;
   }

   return (
      <Table<Evenement>
         dataSource={evenementsEnCours?.items.map((e) => new Evenement(e))}
         loading={isLoading}
         rowKey={(record) => record["@id"] as string}
         pagination={false}
         columns={evenementTableColumns({
            saisieEvtRenfort: false,
            afficherEtatEnvoiRh: false,
            onEvenementSelected: (evenement) => {
               dispatch(setModalEvenementId(evenement["@id"] as string));
            },
         })}
      />
   );
}
