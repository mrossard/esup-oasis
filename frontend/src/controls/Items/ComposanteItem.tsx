/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { Breakpoint, Popover, Space, Tag, Tooltip, Typography } from "antd";
import Spinner from "../Spinner/Spinner";
import useBreakpoint from "antd/es/grid/hooks/useBreakpoint";
import { BankFilled } from "@ant-design/icons";
import { IComposante } from "../../api/ApiTypeHelpers";
import { PREFETCH_COMPOSANTES } from "../../api/ApiPrefetchHelpers";
import { useApi } from "../../context/api/ApiProvider";

interface IItemComposante {
   composante?: IComposante;
   composanteId?: string;
   showAvatar?: boolean;
   responsive?: Breakpoint;
   className?: string;
   ellipsis?: boolean;
   maxWidth?: number;
   popoverContent?: React.ReactElement;
}

/**
 * Renders a ComposanteItem component.
 *
 * @param {IItemComposante} props - The props object.
 * @param {IComposante} [props.composante] - The composante object.
 * @param {string} [props.composanteId] - The composante ID.
 * @param {boolean} [props.showAvatar=true] - Whether to show the avatar or not.
 * @param {string} [props.responsive] - The responsive breakpoint.
 * @param {string} [props.className] - The CSS class name.
 *
 * @return {ReactElement} The rendered ComposanteItem component.
 */
export default function ComposanteItem({
                                          composante,
                                          composanteId,
                                          showAvatar = true,
                                          responsive,
                                          className,
                                          ellipsis,
                                          maxWidth,
                                          popoverContent,
                                       }: IItemComposante): ReactElement {
   const [item, setItem] = useState(composante);
   const { data: composantes } = useApi().useGetCollection(PREFETCH_COMPOSANTES);
   const screens = useBreakpoint();

   useEffect(() => {
      if (composantes) {
         setItem(composantes.items.find((c) => c["@id"] === composanteId));
      }
   }, [composantes, composanteId]);

   useEffect(() => {
      if (composante) setItem(composante);
   }, [composante]);

   if (!item) return <Spinner />;

   if (popoverContent)
      return (
         <Space className={className}>
            {showAvatar && (!responsive || screens[responsive]) && (
               <Popover
                  trigger={["click"]}
                  title="Composante"
                  content={
                     <>
                        <p style={{ maxWidth: 400 }}>{item?.libelle}</p>
                        {popoverContent}
                     </>
                  }
               >
                  <Tag key={item["@id"]} className="bg-app text-text" icon={<BankFilled />}>
                     {ellipsis ? (
                        <Typography.Text
                           ellipsis
                           style={{ maxWidth: maxWidth ?? 175, fontSize: 14 }}
                        >
                           {item?.libelle}
                        </Typography.Text>
                     ) : (
                        item?.libelle
                     )}
                  </Tag>
               </Popover>
            )}
         </Space>
      );

   return (
      <Space className={className}>
         {showAvatar && (!responsive || screens[responsive]) && (
            <Tooltip title={item?.libelle}>
               <Tag key={item["@id"]} className="bg-app text-text" icon={<BankFilled />}>
                  {ellipsis ? (
                     <Typography.Text ellipsis style={{ maxWidth: maxWidth ?? 175 }}>
                        {item?.libelle}
                     </Typography.Text>
                  ) : (
                     item?.libelle
                  )}
               </Tag>
            </Tooltip>
         )}
      </Space>
   );
}
