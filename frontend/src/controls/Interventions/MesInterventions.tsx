/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { useEffect } from "react";
import "../../routes/administration/Administration.scss";
import PlanningWithSider from "../Calendar/PlanningWithSider";
import { PlanningLayout } from "../../redux/context/IAffichageFiltres";
import { useDispatch } from "react-redux";
import { setAffichageFiltres } from "../../redux/actions/AffichageFiltre";
import { useAuth } from "../../auth/AuthProvider";

/**
 * Renders the page for ROLE_GESTIONNAIRE to manage his own interventions.
 */
export default function MesInterventions() {
   const dispatch = useDispatch();
   const auth = useAuth();

   useEffect(() => {
      dispatch(
         setAffichageFiltres({ layout: PlanningLayout.table }, { intervenant: auth.user?.["@id"] }),
      );
      // eslint-disable-next-line react-hooks/exhaustive-deps
   }, []);

   return (
      <>
         <PlanningWithSider saisieEvtRenfort={true} />
      </>
   );
}
