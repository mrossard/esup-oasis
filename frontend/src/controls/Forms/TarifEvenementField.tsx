/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React from "react";
import { Input, Skeleton, Space } from "antd";
import dayjs from "dayjs";
import { useApi } from "../../context/api/ApiProvider";
import { PREFETCH_TYPES_EVENEMENTS } from "../../api/ApiPrefetchHelpers";
import { IEvenement } from "../../api/ApiTypeHelpers";
import { montantToString } from "../../utils/number";

interface ITarifEvenementFieldProps {
   as?: "input" | "text";
   evenement: IEvenement;
}

export default function TarifEvenementField({
   evenement,
   as = "input",
}: ITarifEvenementFieldProps) {
   const { data: typesEvenements, isFetching: isFetchingTypeEvenement } =
      useApi().useGetCollection(PREFETCH_TYPES_EVENEMENTS);
   const { data: taux } = useApi().useGetItem({
      path: "/types_evenements/{typeId}/taux/{id}",
      url: typesEvenements?.items.find((t) => t["@id"] === evenement.type)?.tauxActif as string,
      enabled:
         !!typesEvenements &&
         !!evenement.type &&
         !!typesEvenements?.items.find((t) => t["@id"] === evenement.type)?.tauxActif,
   });

   // get nb minutes between evenement.debut and evenement.fin
   const nbMinutes = dayjs(evenement.fin).diff(dayjs(evenement.debut), "minute");
   const dureeTotale =
      nbMinutes + (evenement.tempsPreparation || 0) + (evenement.tempsSupplementaire || 0);

   if (isFetchingTypeEvenement) {
      return <Skeleton active />;
   }

   if (!taux || !taux?.montant) {
      return (
         <p className="text-warning">
            Pas de taux horaire défini actuellement pour les évènements de la catégorie "
            {typesEvenements?.items.find((t) => t["@id"] === evenement.type)?.libelle}"
         </p>
      );
   }

   if (as === "input") {
      return (
         <>
            <Input
               disabled
               addonAfter={<div style={{ width: 55 }}>€</div>}
               placeholder="0,00"
               className="text-center text-primary semi-bold"
               value={montantToString((dureeTotale / 60).toString(), taux?.montant)}
            />
            <div className="legende">
               Tarif horaire des évènements de la catégorie "
               {typesEvenements?.items.find((t) => t["@id"] === evenement.type)?.libelle}"&nbsp;:{" "}
               {taux?.montant || "?"}
               &nbsp;€ / heure
            </div>
         </>
      );
   }

   return (
      <Space>
         <span>{montantToString((dureeTotale / 60).toString(), taux?.montant)}</span>
         <span>€</span>
      </Space>
   );
}
