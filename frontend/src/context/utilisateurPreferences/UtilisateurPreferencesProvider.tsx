/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { createContext, ReactNode, useContext, useEffect } from "react";
import { useAuth } from "../../auth/AuthProvider";
import { useApi } from "../api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import {
   setAccessibiliteContrast,
   setAccessibiliteDyslexieArial,
   setAccessibiliteDyslexieOpenDys,
   setPoliceLarge,
} from "../../redux/actions/Accessibilite";
import { useDispatch } from "react-redux";
import { queryClient } from "../../App";

export interface UtilisateurPreferencesType {
   getPreference: (cle: string) => string | undefined;
   getPreferenceJson: (cle: string) => object | undefined;
   getPreferenceArray: (cle: string) => any[];
   setPreference: (cle: string, value: string) => void;
   setPreferenceJson: (cle: string, value: object) => void;
   setPreferenceArray: (cle: string, value: any[]) => void;
   preferencesChargees: boolean;
}

const UtilisateurPreferencesContext = createContext<UtilisateurPreferencesType>({
   getPreference: () => "",
   getPreferenceJson: () => ({}),
   getPreferenceArray: () => [],
   setPreference: () => {},
   setPreferenceJson: () => {},
   setPreferenceArray: () => {},
   preferencesChargees: false,
});

export function UtilisateurPreferencesProvider(props: { children: ReactNode }) {
   const auth = useAuth();
   const dispatch = useDispatch();
   const [preferencesChargees, setPreferencesChargees] = React.useState<boolean>(false);
   const { data: preferences } = useApi().useGetCollectionPaginated({
      path: "/utilisateurs/{uid}/parametres_ui",
      parameters: { uid: auth.user?.["@id"] as string },
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
      enabled: !!auth.user,
      onError: (error) => {
         console.error(error);
      },
   });

   const mutatePreference = useApi().usePut({
      path: "/utilisateurs/{uid}/parametres_ui/{cle}",
      onSuccess: () => {
         queryClient.invalidateQueries({ queryKey: ["/utilisateurs/{uid}/parametres_ui"] }).then();
      },
   });

   useEffect(() => {
      if (preferences) {
         setPreferencesChargees(true);
      }
   }, [preferences]);

   // Accessibilité
   useEffect(() => {
      if (preferences) {
         // Rétablissement des préférences d'accessibilité
         const contrast = preferences.items.find(
            (p) => p["@id"] === `${auth.user?.["@id"]}/parametres_ui/contrast`,
         );
         if (contrast) dispatch(setAccessibiliteContrast(contrast?.valeur === "true"));

         const dyslexieArial = preferences.items.find(
            (p) => p["@id"] === `${auth.user?.["@id"]}/parametres_ui/dyslexie-arial`,
         );
         if (dyslexieArial)
            dispatch(setAccessibiliteDyslexieArial(dyslexieArial?.valeur === "true"));

         const dyslexieOpenDys = preferences.items.find(
            (p) => p["@id"] === `${auth.user?.["@id"]}/parametres_ui/dyslexie-opendys`,
         );
         if (dyslexieOpenDys)
            dispatch(setAccessibiliteDyslexieOpenDys(dyslexieOpenDys?.valeur === "true"));

         const policeLarge = preferences.items.find(
            (p) => p["@id"] === `${auth.user?.["@id"]}/parametres_ui/police-large`,
         );
         if (policeLarge) dispatch(setPoliceLarge(policeLarge?.valeur === "true"));
      }
   }, [auth.user, dispatch, preferences]);

   function getPreference(cle: string) {
      return preferences?.items.find(
         (p) => p["@id"] === `${auth.user?.["@id"]}/parametres_ui/${cle}`,
      )?.valeur;
   }

   function getPreferenceJson(cle: string) {
      return JSON.parse(getPreference(cle) || "{}");
   }

   function getPreferenceArray(cle: string) {
      return JSON.parse(getPreference(cle) || "[]");
   }

   function setPreference(cle: string, value: string) {
      mutatePreference.mutate({
         "@id": `${auth.user?.["@id"]}/parametres_ui/${cle}`,
         data: {
            valeur: value,
         },
      });
   }

   function setPreferenceJson(cle: string, value: object) {
      setPreference(cle, JSON.stringify(value));
   }

   function setPreferenceArray(cle: string, value: any[]) {
      setPreference(cle, JSON.stringify(value));
   }

   return (
      <UtilisateurPreferencesContext.Provider
         value={{
            getPreference,
            getPreferenceJson,
            getPreferenceArray,
            setPreference,
            setPreferenceJson,
            setPreferenceArray,
            preferencesChargees,
         }}
      >
         {props.children}
      </UtilisateurPreferencesContext.Provider>
   );
}

export function usePreferences(): UtilisateurPreferencesType {
   return useContext(UtilisateurPreferencesContext);
}
