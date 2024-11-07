/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { ICharteUtilisateur, IDemande } from "../../api/ApiTypeHelpers";
import { App, Button, Card, Checkbox, Form } from "antd";
import React, { useEffect, useState } from "react";
import { useApi } from "../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import Spinner from "../Spinner/Spinner";

export function ValidationCharte(props: { demande: IDemande }) {
   const { message } = App.useApp();
   const { data: chartes } = useApi().useGetCollectionPaginated({
      path: "/utilisateurs/{uid}/chartes",
      parameters: {
         uid: props.demande.demandeur?.["@id"] as string,
      },
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
   });
   const [chartesUtilisateurDemande, setChartesUtilisateurDemande] = useState<ICharteUtilisateur[]>(
      [],
   );

   const mutationAccepterCharte = useApi().usePatch({
      path: `/utilisateurs/{uid}/chartes/{id}`,
      invalidationQueryKeys: ["/utilisateurs/{uid}/chartes", "/demandes"],
      onSuccess: () => {
         message.success("La charte a bien été acceptée.").then();
      },
   });

   useEffect(() => {
      if (chartes) {
         setChartesUtilisateurDemande(
            chartes.items.filter((c) => c.demande === props.demande["@id"]),
         );
      }
   }, [chartes, props.demande]);

   if (!chartesUtilisateurDemande) return <Spinner />;

   return chartesUtilisateurDemande.map((charte) => (
      <Card key={charte["@id"]}>
         <span dangerouslySetInnerHTML={{ __html: charte.contenu as string }} />
         <Form
            onFinish={(values) => {
               // noinspection JSUnresolvedReference
               if (values.accepter) {
                  mutationAccepterCharte.mutate({
                     "@id": charte["@id"] as string,
                     data: { dateValidation: new Date().toISOString() },
                  });
               }
            }}
         >
            <Form.Item
               name="accepter"
               valuePropName="checked"
               required
               rules={[
                  {
                     required: true,
                     message: "Vous devez accepter la charte pour poursuivre.",
                  },
               ]}
            >
               <Checkbox className="semi-bold">
                  J'atteste avoir lu la charte ci-dessus et m'engage à respecter les informations
                  qui y sont contenues.
               </Checkbox>
            </Form.Item>
            <Form.Item name="submit">
               <Button type="primary" htmlType="submit">
                  Accepter la charte
               </Button>
            </Form.Item>
         </Form>
      </Card>
   ));
}
