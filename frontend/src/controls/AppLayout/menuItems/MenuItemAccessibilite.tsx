/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { IAccessibilite } from "../../../redux/context/IAccessibilite";
import { Dispatch } from "redux";
import { MenuProps } from "antd";
import Icon, { CheckOutlined } from "@ant-design/icons";
import {
   setAccessibiliteContrast,
   setAccessibiliteDyslexieArial,
   setAccessibiliteDyslexieOpenDys,
   setPoliceLarge,
} from "../../../redux/actions/Accessibilite";
import React from "react";
import { ReactComponent as IconeAccessibilite } from "../../../assets/images/accessibilite.svg";

/**
 * Generate menu items for accessibility options.
 *
 * @param {IAccessibilite} appAccessibilite - The accessibility settings object.
 * @param {Dispatch} dispatch - The Redux dispatch function.
 * @param setPreference
 * @returns {MenuProps["items"]} - The generated menu items.
 */
export function menuItemAccessibilite(
   appAccessibilite: IAccessibilite,
   dispatch: Dispatch,
   setPreference: (key: string, value: string) => void,
): MenuProps["items"] {
   return [
      {
         key: "accessibilite",
         title: "Options d'accessibilité",
         className: "menu-small-item no-indicator item-accessibilite",
         label: (
            <>
               <Icon component={IconeAccessibilite} className="hide-on-overflow" />
               <span className="show-on-overflow">Accessibilité</span>
            </>
         ),
         children: [
            {
               key: "accessibilite-contraste",
               label: "Contraste",
               icon: (
                  <CheckOutlined className={appAccessibilite.contrast ? "mr-1" : "mr-1 v-hidden"} />
               ),
               onClick: () => {
                  dispatch(setAccessibiliteContrast(!appAccessibilite.contrast));
                  setPreference("contrast", !appAccessibilite.contrast ? "true" : "false");
               },
            },
            {
               key: "divider-accessibilite",
               type: "divider",
            },
            {
               key: "accessibilite-dyslexie",
               label: "Dyslexie (Arial)",
               icon: (
                  <CheckOutlined
                     className={appAccessibilite.dyslexieArial ? "mr-1" : "mr-1 v-hidden"}
                  />
               ),
               onClick: () => {
                  const value = !appAccessibilite.dyslexieArial;
                  dispatch(setAccessibiliteDyslexieArial(value));
                  setPreference("dyslexie-arial", value ? "true" : "false");
                  if (value) setPreference("dyslexie-opendys", "false");
               },
            },
            {
               key: "accessibilite-dyslexie-opendys",
               label: "Dyslexie (OpenDys)",
               icon: (
                  <CheckOutlined
                     className={appAccessibilite.dyslexieOpenDys ? "mr-1" : "mr-1 v-hidden"}
                  />
               ),
               onClick: () => {
                  const value = !appAccessibilite.dyslexieOpenDys;
                  dispatch(setAccessibiliteDyslexieOpenDys(value));
                  setPreference("dyslexie-opendys", value ? "true" : "false");
                  if (value) setPreference("dyslexie-arial", "false");
               },
            },
            {
               key: "divider-accessibilite-2",
               type: "divider",
            },
            {
               key: "accessibilite-police-large",
               label: "Police large",
               icon: (
                  <CheckOutlined
                     className={appAccessibilite.policeLarge ? "mr-1" : "mr-1 v-hidden"}
                  />
               ),
               onClick: () => {
                  const value = !appAccessibilite.policeLarge;
                  dispatch(setPoliceLarge(value));
                  setPreference("police-large", value ? "true" : "false");
               },
            },
         ],
      },
   ];
}
