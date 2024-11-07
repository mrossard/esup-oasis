/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import Spinner from "../../Spinner/Spinner";
import TypeDemandeItem from "../../Items/TypeDemandeItem";
import React from "react";
import { Affix, Card } from "antd";
import AvancementDemandeGestion from "../Avancement/AvancementDemandeGestion";
import { useQuestionnaire } from "../../../context/demande/QuestionnaireProvider";
import { RefsTourDemande } from "../../../routes/gestionnaire/demandeurs/Demande";
import { DossierDemandeTabs } from "./DossierDemandeTabs";

export function DossierDemande(props: {
   refs?: RefsTourDemande;
   affichageTour?: boolean;
}): React.ReactElement {
   const { demande, typeDemande, form } = useQuestionnaire();

   if (!demande || !typeDemande) return <Spinner />;

   return (
      <>
         {props.affichageTour ? (
            <Card className="mt-2">
               <AvancementDemandeGestion refs={props.refs} />
            </Card>
         ) : (
            <Affix offsetTop={75}>
               <Card className="mt-2">
                  <AvancementDemandeGestion />
               </Card>
            </Affix>
         )}
         <Card
            className="mt-2"
            title={<TypeDemandeItem typeDemandeId={demande.typeDemande} showInfos />}
         >
            <DossierDemandeTabs
               demande={demande}
               typeDemande={typeDemande}
               form={form}
               refs={props.refs}
            />
         </Card>
      </>
   );
}
