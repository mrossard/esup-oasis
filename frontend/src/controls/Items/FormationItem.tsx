/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { Breakpoint, Tooltip } from "antd";
import Spinner from "../Spinner/Spinner";
import { useApi } from "../../context/api/ApiProvider";
import { PREFETCH_FORMATIONS } from "../../api/ApiPrefetchHelpers";
import { IFormation } from "../../api/ApiTypeHelpers";
import { EllipsisMiddle } from "../Typography/EllipsisMiddle";

interface IItemFormation {
   formation?: IFormation;
   formationId?: string;
   showAvatar?: boolean;
   responsive?: Breakpoint;
   className?: string;
   styleLibelle?: React.CSSProperties;
}

/**
 * Renders a formation item component.
 *
 * @param {IItemFormation} props - The props for the formation item component.
 * @param {IFormation} [props.formation] - The formation data.
 * @param {string} [props.formationId] - The ID of the formation.
 *
 * @returns {ReactElement} The rendered formation item component.
 */
export default function FormationItem({ formation, formationId }: IItemFormation): ReactElement {
   const [item, setItem] = useState(formation);
   const { data: dataFormation, isFetching } = useApi().useGetCollection(PREFETCH_FORMATIONS);

   useEffect(() => {
      if (dataFormation && formationId) {
         setItem(dataFormation.items.find((t) => t["@id"] === formationId));
      }
   }, [dataFormation, formationId]);

   if (!item || isFetching) return <Spinner />;

   return (
      <Tooltip title={item?.libelle}>
         <EllipsisMiddle content={item?.libelle as string} suffixCount={12} expandable />
      </Tooltip>
   );
}
