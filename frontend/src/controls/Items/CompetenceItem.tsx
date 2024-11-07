/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { Avatar, Breakpoint, Space } from "antd";
import Spinner from "../Spinner/Spinner";
import useBreakpoint from "antd/es/grid/hooks/useBreakpoint";
import { useApi } from "../../context/api/ApiProvider";
import { CrownOutlined } from "@ant-design/icons";
import { PREFETCH_COMPETENCES } from "../../api/ApiPrefetchHelpers";
import { ICompetence } from "../../api/ApiTypeHelpers";

interface IItemCompetence {
   competence?: ICompetence;
   competenceId?: string;
   showAvatar?: boolean;
   responsive?: Breakpoint;
   className?: string;
   styleLibelle?: React.CSSProperties;
}

/**
 * Renders a competence item component.
 *
 * @param {IItemCompetence} props - The props for the competence item component.
 * @param {ICompetence} [props.competence] - The competence data.
 * @param {string} [props.competenceId] - The ID of the competence.
 * @param {boolean} [props.showAvatar=true] - Whether to show the avatar or not.
 * @param {string} [props.responsive] - The breakpoint for responsiveness.
 * @param {string} [props.className] - The class name for styling the component.
 * @param {Object} [props.styleLibelle] - The styling for the competence label.
 *
 * @returns {ReactElement} The rendered competence item component.
 */
export default function CompetenceItem({
                                          competence,
                                          competenceId,
                                          showAvatar = true,
                                          responsive,
                                          className,
                                          styleLibelle,
                                       }: IItemCompetence): ReactElement {
   const [item, setItem] = useState(competence);
   const { data: dataCompetence, isFetching } = useApi().useGetCollection(PREFETCH_COMPETENCES);
   const screens = useBreakpoint();

   useEffect(() => {
      if (dataCompetence && competenceId) {
         setItem(dataCompetence.items.find((t) => t["@id"] === competenceId));
      }
   }, [dataCompetence, competenceId]);

   if (!item || isFetching) return <Spinner />;

   return (
      <Space className={className}>
         {showAvatar && (!responsive || screens[responsive]) && <Avatar icon={<CrownOutlined />} />}
         <span style={styleLibelle}>{item?.libelle}</span>
      </Space>
   );
}
