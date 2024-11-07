/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { Divider, Space } from "antd";
import { TabEvenementParticipantsBeneficiaires } from "./TabEvenementParticipantsBeneficiaires";
import { TabEvenementParticipantsIntervenant } from "./TabEvenementParticipantsIntervenant";
import { TabEvenementParticipantsSuppleants } from "./TabEvenementParticipantsSuppleants";
import { TabEvenementParticipantsEnseignants } from "./TabEvenementParticipantsEnseignants";
import React, { ReactElement } from "react";
import { Evenement } from "../../../lib/Evenement";
import { IPartialEvenement } from "../../../api/ApiTypeHelpers";
import EventMailCopyButton from "../../Buttons/EventMailCopyButton";

interface TabEvenementParticipantsProps {
   evenement: Evenement | undefined;
   setEvenement: (data: IPartialEvenement | undefined, forceResetForm: boolean) => void;
   intervenantDisabled?: boolean;
}

/**
 * Renders the tab for managing event participants.
 *
 * @param {TabEvenementParticipantsProps} props - The component props.
 * @param {Evenement} [props.evenement] - The event object.
 * @param {Function} props.setEvenement - The function to update the event object.
 * @param {boolean} [props.intervenantDisabled] - Flag indicating if the intervenant section is disabled.
 *
 * @returns {ReactElement} The rendered tab component.
 */
export function TabEvenementParticipants({
   evenement,
   setEvenement,
   intervenantDisabled,
}: TabEvenementParticipantsProps): ReactElement {
   return (
      <>
         <Divider>
            <Space>
               Participants
               <EventMailCopyButton evenement={evenement} />
            </Space>
         </Divider>

         <TabEvenementParticipantsBeneficiaires
            evenement={evenement}
            setEvenement={setEvenement}
            title={<div className="semi-bold">Bénéficiaires</div>}
         />

         <TabEvenementParticipantsIntervenant
            evenement={evenement}
            setEvenement={setEvenement}
            intervenantDisabled={intervenantDisabled}
         />

         <TabEvenementParticipantsSuppleants evenement={evenement} setEvenement={setEvenement} />

         <TabEvenementParticipantsEnseignants evenement={evenement} setEvenement={setEvenement} />
      </>
   );
}
