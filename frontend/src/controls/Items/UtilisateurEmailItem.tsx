/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import Spinner from "../Spinner/Spinner";
import { useApi } from "../../context/api/ApiProvider";
import { IUtilisateur } from "../../api/ApiTypeHelpers";

interface IItemIntervenant {
   utilisateur?: IUtilisateur;
   utilisateurId?: string | null;
   emailPerso: boolean;
}

/**
 * Fetches the email of a user from the server using their ID.
 *
 * @param {Object} params - The parameters for fetching the email.
 * @param {IUtilisateur} [params.utilisateur] - The user object.
 * @param {string} [params.utilisateurId] - The ID of the user.
 * @return {string|ReactElement} The email of the user.
 */
export default function UtilisateurEmailItem({
   utilisateur,
   utilisateurId,
   emailPerso,
}: IItemIntervenant): string | ReactElement {
   const [item, setItem] = useState(utilisateur);
   const { data } = useApi().useGetItem({
      path: "/utilisateurs/{uid}",
      url: utilisateurId as string,
      enabled: !!utilisateurId,
   });

   useEffect(() => {
      if (data) {
         setItem(data);
      }
   }, [data]);

   if (!utilisateur && !utilisateurId) return <></>;

   if (!item) return <Spinner />;

   if (emailPerso) {
      return item?.emailPerso as string;
   }

   return item?.email as string;
}
