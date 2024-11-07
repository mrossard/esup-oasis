/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React from "react";
import { Input } from "antd";
import dayjs from "dayjs";
import { IEvenement } from "../../api/ApiTypeHelpers";

interface IDureeTotaleEvenementFieldProps {
   evenement: IEvenement;
}

export default function DureeTotaleEvenementField({ evenement }: IDureeTotaleEvenementFieldProps) {
   // get nb minutes between evenement.debut and evenement.fin
   const nbMinutes = dayjs(evenement.fin).diff(dayjs(evenement.debut), "minute");
   const dureeTotale =
      nbMinutes + (evenement.tempsPreparation || 0) + (evenement.tempsSupplementaire || 0);

   return (
      <>
         <Input
            addonAfter={dureeTotale > 1 ? "minutes" : "minute"}
            className="text-center text-primary semi-bold"
            disabled
            value={dureeTotale}
         />
         <br />
         <div className="legende">= {(dureeTotale / 60).toFixed(1)} heure(s)</div>
      </>
   );
}
