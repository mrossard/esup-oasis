/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { RoleValues, Utilisateur } from "../../lib/Utilisateur";
import { Breakpoint, Space } from "antd";
import Spinner from "../Spinner/Spinner";
import useBreakpoint from "antd/es/grid/hooks/useBreakpoint";
import { MinusOutlined } from "@ant-design/icons";
import { useApi } from "../../context/api/ApiProvider";
import { IUtilisateur } from "../../api/ApiTypeHelpers";
import UtilisateurContent from "./UtilisateurItemContent";
import UtilisateurAvatarImage from "../Avatars/UtilisateurAvatarImage";
import { entiteParent } from "../../api/Utils";

interface IItemEtudiant {
   utilisateur?: IUtilisateur;
   utilisateurId?: string;
   profilBeneficiaireId?: string;

   showAvatar?: boolean;
   showEmail?: boolean;
   showTelephone?: boolean;
   responsive?: Breakpoint;
   role?: RoleValues;
   highlight?: string;
}

/**
 * EtudiantItem component.
 */
export default function EtudiantItem({
   utilisateur,
   utilisateurId,
   showAvatar = true,
   showEmail = false,
   showTelephone = false,
   responsive,
   profilBeneficiaireId,
   role,
   highlight,
}: IItemEtudiant): ReactElement {
   const [id, setId] = useState<string | undefined>(utilisateurId);
   const [item, setItem] = useState(utilisateur);
   const { data } = useApi().useGetItem({
      path: "/utilisateurs/{uid}",
      url: id as string,
      enabled: !!id,
   });
   const screens = useBreakpoint();

   useEffect(() => {
      if (data) {
         setItem(data);
      }
   }, [data]);

   useEffect(() => {
      if (profilBeneficiaireId) {
         setId(entiteParent(profilBeneficiaireId));
      }
   }, [profilBeneficiaireId]);

   if (!utilisateur && !id) return <MinusOutlined aria-label="Aucun utilisateur" />;

   if (!item) return <Spinner />;

   const user = new Utilisateur(item);

   return (
      <Space>
         {showAvatar && (!responsive || screens[responsive]) && (
            <UtilisateurAvatarImage
               as="img"
               width={48}
               utilisateur={item as IUtilisateur}
               size={48}
               role={role || user.roleCalcule}
               className="border-0"
            />
         )}
         <UtilisateurContent
            utilisateur={item}
            showEmail={showEmail}
            showTelephone={showTelephone}
            highlight={highlight}
         />
      </Space>
   );
}
