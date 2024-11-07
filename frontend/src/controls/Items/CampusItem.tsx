/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { Breakpoint, Space, Typography } from "antd";
import Spinner from "../Spinner/Spinner";
import { CampusAvatar } from "../Avatars/CampusAvatar";
import useBreakpoint from "antd/es/grid/hooks/useBreakpoint";
import { useApi } from "../../context/api/ApiProvider";
import { PREFETCH_CAMPUS } from "../../api/ApiPrefetchHelpers";
import { ICampus } from "../../api/ApiTypeHelpers";

/**
 * Interface representing a campus.
 * @interface
 */
interface IItemCampus {
   campus?: ICampus;
   campusId?: string;
   showAvatar?: boolean;
   responsive?: Breakpoint;
   className?: string;
   salle?: string | null;
}

/**
 * Renders a campus item with optional avatar and salle information.
 *
 * @param {IItemCampus} options - The options for rendering the campus item.
 * @param {ICampus} [options.campus] - The campus data.
 * @param {string} [options.campusId] - The ID of the campus.
 * @param {boolean} [options.showAvatar=true] - Whether to show the avatar or not.
 * @param {string} [options.responsive] - The breakpoint at which to show the avatar.
 * @param {string} [options.className] - The additional CSS class name for styling.
 * @param {string} [options.salle] - The additional salle information to display.
 *
 * @returns {ReactElement} The rendered campus item component.
 */
export default function CampusItem({
                                      campus,
                                      campusId,
                                      showAvatar = true,
                                      responsive,
                                      className,
                                      salle,
                                   }: IItemCampus): ReactElement {
   const [item, setItem] = useState(campus);
   const { data: dataCampus, isFetching } = useApi().useGetCollection(PREFETCH_CAMPUS);
   const screens = useBreakpoint();

   useEffect(() => {
      if (dataCampus && campusId) {
         setItem(dataCampus.items.find((t) => t["@id"] === campusId));
      }
   }, [dataCampus, campusId]);

   if (!item || isFetching) return <Spinner />;

   return (
      <Space className={className}>
         {showAvatar && (!responsive || screens[responsive]) && <CampusAvatar campus={item} />}
         <span>
            {item?.libelle}
            {salle && <Typography.Text type="secondary"> &bull; {salle}</Typography.Text>}
         </span>
      </Space>
   );
}
