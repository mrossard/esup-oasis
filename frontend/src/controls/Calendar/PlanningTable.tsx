/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { useEffect } from "react";
import PlanningWithSider from "./PlanningWithSider";
import { PlanningLayout } from "../../redux/context/IAffichageFiltres";
import { useDispatch } from "react-redux";
import { setAffichage } from "../../redux/actions/AffichageFiltre";

export default function PlanningTable() {
   const dispatch = useDispatch();

   useEffect(() => {
      dispatch(setAffichage({ layout: PlanningLayout.table }));
      // eslint-disable-next-line react-hooks/exhaustive-deps
   }, []);

   return <PlanningWithSider />;
}
