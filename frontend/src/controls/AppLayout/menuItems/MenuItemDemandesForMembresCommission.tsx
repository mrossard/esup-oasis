/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { NavigateFunction } from "react-router-dom";
import { Button, MenuProps } from "antd";
import React from "react";

export const menuItemDemandesForMembresCommission = (
   setSelectedKey: (key: string) => void,
   navigate: NavigateFunction,
): MenuProps["items"] => [
   {
      key: "demandeurs",
      className: "mr-auto",
      children: [],
      popupClassName: "d-none",
      label: (
         <Button
            type="text"
            className="no-hover p-0"
            onClick={() => {
               setSelectedKey("demandeurs");
               navigate("/demandes");
            }}
         >
            Demandes
         </Button>
      ),
   },
];
