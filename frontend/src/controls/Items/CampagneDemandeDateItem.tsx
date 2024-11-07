/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { useEffect, useState } from "react";
import { ICampagneDemande } from "../../api/ApiTypeHelpers";
import { useApi } from "../../context/api/ApiProvider";
import dayjs from "dayjs";

export default function CampagneDemandeDateItem(props: {
   campagneDemande?: ICampagneDemande;
   campagneDemandeId?: string;
   templateString: string;
}) {
   const [item, setItem] = useState<ICampagneDemande | undefined>(props.campagneDemande);
   const { data: campagneDemandeData } = useApi().useGetItem({
      path: "/types_demandes/{typeId}/campagnes/{id}",
      url: props.campagneDemandeId,
      enabled: !!props.campagneDemandeId,
   });
   useEffect(() => {
      if (campagneDemandeData) {
         setItem(campagneDemandeData);
      }
   }, [campagneDemandeData]);

   if (!item) return null;

   return props.templateString.replace(
      /{{(.*?)}}/g,
      (match, p1: "debut" | "fin" | "dateCommission") =>
         dayjs(item?.[p1]).format("DD/MM/YYYY") ?? match,
   );
}
