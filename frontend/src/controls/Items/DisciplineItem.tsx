/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { Breakpoint, Tag, Tooltip } from "antd";
import Spinner from "../Spinner/Spinner";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { IDiscipline } from "../../api/ApiTypeHelpers";

interface IItemDiscipline {
   discipline?: IDiscipline;
   disciplineId?: string;
   showAvatar?: boolean;
   responsive?: Breakpoint;
   className?: string;
   styleLibelle?: React.CSSProperties;
}

/**
 * Renders a discipline item component.
 *
 * @param {IItemDiscipline} props - The props for the discipline item component.
 * @param {IDiscipline} [props.discipline] - The discipline data.
 * @param {string} [props.disciplineId] - The ID of the discipline.
 *
 * @returns {ReactElement} The rendered discipline item component.
 */
export default function DisciplineItem({
                                          discipline,
                                          disciplineId,
                                       }: IItemDiscipline): ReactElement {
   const [item, setItem] = useState(discipline);
   const { data: dataDiscipline, isFetching } = useApi().useGetCollectionPaginated({
      path: "/disciplines_sportives",
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
   });

   useEffect(() => {
      if (dataDiscipline && disciplineId) {
         setItem(dataDiscipline.items.find((t) => t["@id"] === disciplineId));
      }
   }, [dataDiscipline, disciplineId]);

   if (!item || isFetching) return <Spinner />;

   return (
      <Tooltip title={item?.libelle}>
         <Tag>{item?.libelle}</Tag>
      </Tooltip>
   );
}
