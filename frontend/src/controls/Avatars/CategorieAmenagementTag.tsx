/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { useApi } from "../../context/api/ApiProvider";
import { PREFETCH_CATEGORIES_AMENAGEMENTS } from "../../api/ApiPrefetchHelpers";
import Spinner from "../Spinner/Spinner";
import { Tag } from "antd";
import React from "react";

export function CategorieAmenagementTag(props: { categorieId: undefined | string }) {
   const { data: categories } = useApi().useGetCollection(PREFETCH_CATEGORIES_AMENAGEMENTS);

   if (!categories) return <Spinner />;

   return <Tag>{categories?.items.find((c) => c["@id"] === props.categorieId)?.libelle}</Tag>;
}
