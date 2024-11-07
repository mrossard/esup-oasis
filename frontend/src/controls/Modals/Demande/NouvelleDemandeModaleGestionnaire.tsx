/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { Button, Flex, Modal, Space } from "antd";
import { useApi } from "../../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../../constants";
import { ITypeDemande } from "../../../api/ApiTypeHelpers";
import useBreakpoint from "antd/es/grid/hooks/useBreakpoint";
import React, { useState } from "react";
import { TypesDemandesListItems } from "./TypesDemandesListItems";
import UtilisateurFormItemSelect from "../../Forms/UtilisateurFormItemSelect";
import { RoleValues } from "../../../lib/Utilisateur";
import { UtilisateurAvatar } from "../../Avatars/UtilisateurAvatar";
import Spinner from "../../Spinner/Spinner";
import { ClearOutlined } from "@ant-design/icons";

export default function NouvelleDemandeModaleGestionnaire(props: {
   open: boolean;
   setOpen: (open: boolean) => void;
   onClose?: () => Promise<void>;
}) {
   const screens = useBreakpoint();
   const [demandeurId, setDemandeurId] = useState<string>();
   const { data: typesDemandes } = useApi().useGetCollectionPaginated({
      path: "/types_demandes",
      enabled: true,
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
   });

   const { data: demandeur } = useApi().useGetItem({
      path: "/utilisateurs/{uid}",
      url: demandeurId,
      enabled: !!demandeurId,
   });

   function getTypesDemandesPostulables(): ITypeDemande[] {
      return (typesDemandes?.items || []).filter((typeDemande) => {
         return typeDemande.actif;
      });
   }

   if (!typesDemandes) return null;
   if (!props.open) return null;

   return (
      <Modal
         centered
         title={
            <h2 autoFocus className="m-0">
               Nouvelle demande
            </h2>
         }
         open={props.open}
         onCancel={() => {
            props.setOpen(false);
         }}
         onOk={() => {
            if (props.onClose === undefined) {
               props.setOpen(false);
               return;
            }

            props.onClose().then(() => {
               props.setOpen(false);
            });
         }}
         width={screens.lg ? 800 : "95%"}
         okText="Fermer"
         cancelButtonProps={{ style: { display: "none" } }}
      >
         <p>
            En tant que gestionnaire, vous pouvez déposer une demande pour un demandeur.
            Sélectionnez un demandeur puis le type de demande que vous souhaitez déposer.
         </p>
         {!demandeurId ? (
            <>
               <UtilisateurFormItemSelect
                  style={{ width: "100%", marginBottom: 8 }}
                  onSelect={(value) => {
                     setDemandeurId(value);
                  }}
                  existeNumeroEtudiant={true}
                  placeholder="Rechercher un demandeur"
               />
            </>
         ) : (
            <Flex justify="space-between">
               {demandeur ? (
                  <Space>
                     <UtilisateurAvatar utilisateur={demandeur} role={RoleValues.ROLE_DEMANDEUR} />
                     <span>
                        <span className="semi-bold">{demandeur?.nom?.toLocaleUpperCase()}</span>{" "}
                        {demandeur?.prenom}
                     </span>
                  </Space>
               ) : (
                  <Spinner />
               )}
               <Button icon={<ClearOutlined />} onClick={() => setDemandeurId(undefined)}>
                  Changer de demandeur
               </Button>
            </Flex>
         )}

         {demandeur && (
            <TypesDemandesListItems
               titre="Types de demandes"
               items={getTypesDemandesPostulables()}
               ajout
               demandeurId={demandeur["@id"] as string}
            />
         )}
      </Modal>
   );
}
