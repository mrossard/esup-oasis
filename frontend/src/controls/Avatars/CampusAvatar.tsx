/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { memo, ReactElement, useEffect, useState } from "react";
import { Avatar } from "antd";
import { BankOutlined } from "@ant-design/icons";
import { useApi } from "../../context/api/ApiProvider";
import { PREFETCH_CAMPUS } from "../../api/ApiPrefetchHelpers";
import { ICampus } from "../../api/ApiTypeHelpers";

interface IAvatarCampus {
   campus?: ICampus;
   campusId?: string;
}

/**
 * React a campus avatar.
 *
 * @component
 * @param {IAvatarCampus} props - The component props.
 * @param {ICampus} [props.campus] - The campus object.
 * @param {string} [props.campusId] - The ID of the campus.
 * @returns {ReactElement} - The rendered component.
 */
export const CampusAvatar: React.FC<IAvatarCampus> = memo(
   ({ campus, campusId }: IAvatarCampus): ReactElement => {
      const [campusData, setCampusData] = useState<ICampus | undefined>(campus);
      const { data: dataCampus, isFetching } = useApi().useGetCollection(PREFETCH_CAMPUS);

      useEffect(() => {
         if (dataCampus && campusId) {
            setCampusData(dataCampus.items.find((t) => t["@id"] === campusId));
         }
         // eslint-disable-next-line react-hooks/exhaustive-deps
      }, [dataCampus, campusId]);

      useEffect(() => {
         if (campus) setCampusData(campus);
      }, [campus]);

      if (isFetching || campusData === undefined) {
         return <Avatar className="bg-primary" icon={<BankOutlined />} />;
      }

      return (
         <Avatar data-testid={campusData.libelle} className="bg-primary" aria-hidden>
            {campusData.libelle[0].toUpperCase()}
         </Avatar>
      );
   },
   (prevProps, nextProps) =>
      prevProps.campusId === nextProps.campusId &&
      JSON.stringify(prevProps.campus) === JSON.stringify(nextProps.campus),
);
