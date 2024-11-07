/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useMemo, useState } from "react";
import { Menu, MenuProps } from "antd";
import { useAuth } from "../../auth/AuthProvider";
import { useNavigate } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { IStore } from "../../redux/Store";
import { useApi } from "../../context/api/ApiProvider";
import { IAccessibilite } from "../../redux/context/IAccessibilite";
import { useIsFetching } from "@tanstack/react-query";
import PageTitle from "../../utils/PageTitle/PageTitle";
import { usePreferences } from "../../context/utilisateurPreferences/UtilisateurPreferencesProvider";
import { useWait } from "../../utils/Wait/useWait";
import { menuItemNotifications } from "./menuItems/MenuItemNotifications";
import { menuItemAccessibilite } from "./menuItems/MenuItemAccessibilite";
import { menuItemLogo } from "./menuItems/MenuItemLogo";
import { menuItemPlanningBeneficiaireIntervenant } from "./menuItems/MenuItemPlanningBeneficiaireIntervenant";
import { menuItemDemandeur } from "./menuItems/MenuItemDemandeur";
import { menuItemServicesFaitsIntervenant } from "./menuItems/MenuItemServicesFaitsIntervenant";
import { menuItemPlanningPlanificateur } from "./menuItems/MenuItemPlanningPlanificateur";
import { menuItemBeneficiaires } from "./menuItems/MenuItemBeneficiaires";
import { menuItemAmenagementsForReferents } from "./menuItems/MenuItemAmenagementsForReferents";
import { menuItemDemandesForMembresCommission } from "./menuItems/MenuItemDemandesForMembresCommission";
import { menuItemIntervenants } from "./menuItems/MenuItemIntervenants";
import { menuItemDemandeurs } from "./menuItems/MenuItemDemandeurs";
import { menuItemRecherche } from "./menuItems/MenuItemRecherche";
import { menuItemUtilisateur } from "./menuItems/MenuItemUtilisateur";

/**
 * Render the application's horizontal menu layout.
 *
 * @return {ReactElement} The rendered menu component.
 */
export default function AppLayoutMenu(): ReactElement {
   const auth = useAuth();
   const navigate = useNavigate();
   const dispatch = useDispatch();
   const apiFetching = useIsFetching();
   const appAccessibilite: IAccessibilite = useSelector(
      ({ accessibilite }: Partial<IStore>) => accessibilite,
   ) as IAccessibilite;
   const [selectedKey, setSelectedKey] = useState<string>();
   const [modeRecherche, setModeRecherche] = useState(false);
   const { setPreference } = usePreferences();
   const wait = useWait(2500);

   const { data: stats, isFetching: isFetchingStats } = useApi().useGetItem({
      path: "/statistiques",
      url: "/statistiques",
      query: {
         utilisateur: auth.user?.["@id"] as string,
      },
      // Les bénéficiaires n'ont pas accès aux stats
      // Bugfix lors de l'impersonate
      enabled:
         !wait &&
         !!auth.user?.["@id"] &&
         (auth.user?.isPlanificateur || auth.user?.isIntervenant) &&
         !auth.impersonate,
   });

   const menuItems: MenuProps["items"] = useMemo(() => {
      const items = [];

      items.push(...(menuItemLogo(setSelectedKey, navigate) || []));
      // Spécifique pour Planificateurs
      if (auth.user?.isPlanificateur) {
         items.push(...(menuItemDemandeurs(setSelectedKey, navigate) || []));
         items.push(...(menuItemBeneficiaires(setSelectedKey, navigate, auth.user) || []));
         items.push(...(menuItemIntervenants(setSelectedKey, navigate) || []));
         items.push(...(menuItemPlanningPlanificateur(setSelectedKey, auth.user, navigate) || []));
      }

      if (auth.user?.isDemandeur) {
         items.push(
            ...(menuItemDemandeur(
               setSelectedKey,
               navigate,
               auth.user?.isBeneficiaire || auth.user?.isIntervenant ? "" : "mr-auto",
            ) || []),
         );
      }

      if (auth.user?.isBeneficiaire && !auth.user?.isPlanificateur) {
         items.push(
            ...(menuItemPlanningBeneficiaireIntervenant(setSelectedKey, navigate, "mr-auto") || []),
         );
      } else if (auth.user?.isIntervenant && !auth.user?.isPlanificateur) {
         items.push(...(menuItemPlanningBeneficiaireIntervenant(setSelectedKey, navigate) || []));
         items.push(
            ...(menuItemServicesFaitsIntervenant(setSelectedKey, navigate, "mr-auto") || []),
         );
      }

      if (auth.user?.isCommissionMembre && !auth.user?.isPlanificateur) {
         items.push(...(menuItemDemandesForMembresCommission(setSelectedKey, navigate) || []));
      }

      if (auth.user?.isReferentComposante && !auth.user?.isPlanificateur) {
         items.push(...(menuItemAmenagementsForReferents(setSelectedKey, navigate) || []));
      }

      // Recherche
      if (auth.user?.isPlanificateur) {
         items.push(
            ...(menuItemRecherche(dispatch, modeRecherche, setModeRecherche, auth.user, navigate) ||
               []),
         );
      }

      // Accessibilité
      items.push(...(menuItemAccessibilite(appAccessibilite, dispatch, setPreference) || []));

      return items;
      // eslint-disable-next-line react-hooks/exhaustive-deps
   }, [dispatch, modeRecherche, navigate, auth, appAccessibilite]);

   const menuNotifications: MenuProps["items"] = useMemo(() => {
      if (!auth.user || !auth.user.isPlanificateur) return [];
      return menuItemNotifications(auth.user, navigate, dispatch, stats, isFetchingStats) || [];
   }, [auth.user, dispatch, isFetchingStats, navigate, stats]);

   // --- Rendre le menu accessible (fix ant design) ---
   useEffect(() => {
      // Aria label pour le menu accessibilité
      const menuAccessibiliteCtrl = document.querySelector(
         ".item-accessibilite div[role=menuitem]",
      );
      if (menuAccessibiliteCtrl) menuAccessibiliteCtrl.ariaLabel = "Menu accessibilité";

      // Aria label pour l'overflow du menu'
      const menuOverflow = document.querySelector(".ant-menu-overflow-item-rest");
      if (menuOverflow) {
         menuOverflow.ariaLabel = "Autres items du menu";
         menuOverflow.role = "menuitem";
      }
   });
   // --- /Rendre le menu accessible ---

   return (
      <>
         <PageTitle setSelectedMenuKey={setSelectedKey} />
         <Menu
            selectedKeys={selectedKey ? [selectedKey] : []}
            mode="horizontal"
            items={[
               ...menuItems,
               ...menuNotifications,
               ...menuItemUtilisateur(setSelectedKey, auth, apiFetching, navigate),
            ]}
         />
      </>
   );
}
