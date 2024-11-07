/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { Col, Form, Input, Row } from "antd";
import React, { ReactElement, useEffect, useState } from "react";
import CampusFilter from "../Filters/Campus/CampusFilter";
import { useApi } from "../../context/api/ApiProvider";
import { Evenement } from "../../lib/Evenement";
import { IPartialEvenement } from "../../api/ApiTypeHelpers";
import { PREFETCH_TYPES_EVENEMENTS } from "../../api/ApiPrefetchHelpers";
import { CategorieSelectWithAvatar } from "../Forms/CategorieSelectWithAvatar";
import { TabEvenementParticipants } from "./SubTabs/TabEvenementParticipants";
import { TabEvenementPlanification } from "./SubTabs/TabEvenementPlanification";

interface ITabEvenementInformations {
   evenement: Evenement | undefined;
   setEvenement: (data: IPartialEvenement | undefined, forceResetForm: boolean) => void;
   intervenantDisabled?: boolean;
   formIsDirty: boolean;
}

/**
 * Renders the tab for event information.
 *
 * @param {Object} options - The options for the tab.
 * @param {Evenement} [options.evenement] - The event object.
 * @param {Function} options.setEvenement - The function to update the event object.
 * @param {boolean} [options.intervenantDisabled] - Indicates whether the intervenant is disabled or not.
 * @param {boolean} options.formIsDirty - Indicates whether the form is dirty or not.
 *
 * @return {ReactElement} The rendered tab.
 */
export default function TabEvenementInformations({
   evenement,
   setEvenement,
   intervenantDisabled,
   formIsDirty,
}: ITabEvenementInformations): ReactElement {
   const { data: categories } = useApi().useGetCollection(PREFETCH_TYPES_EVENEMENTS);

   const [typeSelectionne, setTypeSelectionne] = useState(evenement?.type);

   useEffect(() => {
      setTypeSelectionne(evenement?.type);
   }, [evenement]);

   return (
      <>
         <div
            className={`p-2 border-2 border-radius border-${
               categories?.items.find((c) => c["@id"] === typeSelectionne)?.couleur
            }-light bg-${
               categories?.items.find((c) => c["@id"] === typeSelectionne)?.couleur
            }-xlight`}
         >
            <CategorieSelectWithAvatar
               typeSelectionne={typeSelectionne}
               setTypeSelectionne={setTypeSelectionne}
            />

            <Form.Item name="libelle" label="Libellé">
               <Input className="semi-bold" />
            </Form.Item>
            <Row gutter={32}>
               <Col md={12} xs={24} sm={24}>
                  <Form.Item
                     name="campus"
                     label="Campus"
                     rules={[
                        {
                           required: true,
                        },
                     ]}
                  >
                     <CampusFilter />
                  </Form.Item>
               </Col>
               <Col md={12} xs={24} sm={24}>
                  <Form.Item name="salle" label="Salle">
                     <Input />
                  </Form.Item>
               </Col>
            </Row>
         </div>
         <Row gutter={32} className="">
            <Col md={12} xs={24} sm={24}>
               <TabEvenementPlanification
                  evenement={evenement}
                  setEvenement={setEvenement}
                  formIsDirty={formIsDirty}
               />
            </Col>
            <Col md={12} xs={24} sm={24}>
               <TabEvenementParticipants
                  evenement={evenement}
                  setEvenement={setEvenement}
                  intervenantDisabled={intervenantDisabled}
               />
            </Col>
         </Row>
      </>
   );
}
