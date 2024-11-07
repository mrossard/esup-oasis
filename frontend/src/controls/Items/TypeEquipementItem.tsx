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
import { PREFETCH_TYPES_EQUIPEMENTS } from "../../api/ApiPrefetchHelpers";
import { ITypeEquipement } from "../../api/ApiTypeHelpers";

interface IItemTypeEquipement {
   typeEquipement?: ITypeEquipement;
   typeEquipementId?: string;
}

/**
 * Renders the TypeEquipementItem component.
 *
 * @param {Object} props - The input object.
 * @param {ITypeEquipement} [props.typeEquipement] - The type of equipment.
 * @param {string} [props.typeEquipementId] - The ID of the type of equipment.
 *
 * @returns {ReactElement} The rendered TypeEquipementItem component.
 */
export default function TypeEquipementItem({
                                              typeEquipement,
                                              typeEquipementId,
                                           }: IItemTypeEquipement): ReactElement {
   const [item, setItem] = useState(typeEquipement);
   const { data: typeEquipementData } = useApi().useGetCollection(PREFETCH_TYPES_EQUIPEMENTS);

   useEffect(() => {
      if (typeEquipementData) {
         setItem(typeEquipementData.items.find((x) => x["@id"] === typeEquipementId));
      }
      // eslint-disable-next-line react-hooks/exhaustive-deps
   }, [typeEquipementData]);

   if (!item) return <Spinner />;

   return (
      <div>
         <span>{item?.libelle}</span>
      </div>
   );
}
