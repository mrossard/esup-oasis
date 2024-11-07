/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useState } from "react";
import { Button, Grid, Tag } from "antd";
import "./BreakPoint.scss";
import { CloseOutlined, EllipsisOutlined } from "@ant-design/icons";

const { useBreakpoint } = Grid;

/**
 * (seulement pour le dev) Affiche les points de rupture de l'application.
 *
 * @return {ReactElement} - The rendered break points.
 */
function BreakPoint(): ReactElement {
   const screens = useBreakpoint();
   const [open, setOpen] = useState(false);
   return (
      <div className="breakpoints hide-on-print d-flex-center">
         {open &&
            Object.entries(screens).map((screen) => (
               <Tag color={screen[1] ? "success" : "#edeef3"} key={screen[0]}>
                  {screen[0]}
               </Tag>
            ))}
         <Button
            icon={open ? <CloseOutlined /> : <EllipsisOutlined />}
            size="small"
            className="mb-0"
            shape="circle"
            onClick={() => setOpen(!open)}
         />
      </div>
   );
}

export default BreakPoint;
