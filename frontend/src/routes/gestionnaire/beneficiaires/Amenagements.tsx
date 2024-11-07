/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useEffect, useState } from "react";
import { Flex, Layout, Segmented, Typography } from "antd";
import AmenagementTableLayout from "../../../controls/Table/AmenagementTableLayout";
import { useAuth } from "../../../auth/AuthProvider";
import { useSearchParams } from "react-router-dom";

export enum ModeAffichageAmenagement {
   ParAmenagement = "amenagement",
   ParBeneficiaire = "beneficiaire",
}

/**
 * Renders the page for ROLE_GESTIONNAIRE to manage beneficiaries.
 *
 * @returns {ReactElement} The rendered Beneficiaires component.
 */
export default function Amenagements(): ReactElement {
   const user = useAuth().user;
   const [searchParams] = useSearchParams();
   const [modeAffichage, setModeAffichage] = useState<ModeAffichageAmenagement>(
      user?.isGestionnaire || user?.isReferentComposante
         ? ModeAffichageAmenagement.ParBeneficiaire
         : ModeAffichageAmenagement.ParAmenagement,
   );

   useEffect(() => {
      if (searchParams.get("mode")) {
         setModeAffichage(searchParams.get("mode") as ModeAffichageAmenagement);
      }
   }, [searchParams]);

   return (
      <Layout.Content className="amenagements" style={{ padding: "0 50px" }}>
         <Flex justify="space-between" align="center">
            <Typography.Title level={1}>Aménagements</Typography.Title>
            {user?.isGestionnaire && (
               <Segmented
                  className="float-right"
                  options={[
                     { label: "Par bénéficiaire", value: ModeAffichageAmenagement.ParBeneficiaire },
                     { label: "Par aménagement", value: ModeAffichageAmenagement.ParAmenagement },
                  ]}
                  value={modeAffichage}
                  onChange={(value) => setModeAffichage(value as ModeAffichageAmenagement)}
               />
            )}
         </Flex>

         <AmenagementTableLayout modeAffichage={modeAffichage} />
      </Layout.Content>
   );
}
