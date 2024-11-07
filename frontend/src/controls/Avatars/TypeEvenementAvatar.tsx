/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { memo, ReactElement, useEffect, useState } from "react";
import { Avatar } from "antd";
import { IAccessibilite } from "../../redux/context/IAccessibilite";
import { useSelector } from "react-redux";
import { IStore } from "../../redux/Store";
import { useApi } from "../../context/api/ApiProvider";
import Spinner from "../Spinner/Spinner";
import { PREFETCH_TYPES_EVENEMENTS } from "../../api/ApiPrefetchHelpers";
import { ITypeEvenement } from "../../api/ApiTypeHelpers";

interface IAvatarTypeEvenement {
   typeEvenement?: ITypeEvenement;
   typeEvenementId?: string;
   size?: number;
   className?: string;
   icon?: React.ReactNode;
   style?: React.CSSProperties;
}

/**
 * Renders an avatar component for a given event type
 * @param {IAvatarTypeEvenement} props - The properties passed to the component
 * @param {ITypeEvenement} [props.typeEvenement] - The event type
 * @param {string} [props.typeEvenementId] - The event type ID
 * @param {number} [props.size] - The size of the avatar
 * @param {string} [props.className] - The class name of the avatar
 * @param {React.ReactNode} [props.icon] - The icon to display in the avatar
 * @param {React.CSSProperties} [props.style] - The style of the avatar
 * @returns {ReactElement} - The generated avatar component
 */
export const TypeEvenementAvatar = memo(
   ({
      typeEvenement,
      typeEvenementId,
      size,
      className,
      icon,
      style,
   }: IAvatarTypeEvenement): ReactElement => {
      const [typeEvenementData, setTypeEvenementData] = useState<ITypeEvenement | undefined>(
         typeEvenement,
      );
      const { data: typesEvenements, isFetching } =
         useApi().useGetCollection(PREFETCH_TYPES_EVENEMENTS);

      const appAccessibilite: IAccessibilite = useSelector(
         ({ accessibilite }: Partial<IStore>) => accessibilite,
      ) as IAccessibilite;

      useEffect(() => {
         if (typesEvenements && typeEvenementId) {
            setTypeEvenementData(typesEvenements.items.find((t) => t["@id"] === typeEvenementId));
         }
         // eslint-disable-next-line react-hooks/exhaustive-deps
      }, [typesEvenements, typeEvenementId]);

      useEffect(() => {
         if (typeEvenement) setTypeEvenementData(typeEvenement);
      }, [typeEvenement]);

      if (isFetching || !typeEvenementData) {
         return (
            <div className={className}>
               <Spinner />
            </div>
         );
      }

      return (
         <Avatar
            data-testid={typeEvenementData?.libelle}
            size={size}
            aria-hidden
            className={className}
            icon={icon}
            style={{
               ...style,
               backgroundColor: appAccessibilite.contrast
                  ? `var(--color-dark-${typeEvenementData?.couleur})`
                  : `var(--color-${typeEvenementData?.couleur})`,
            }}
         />
      );
   },
   (prevProps, nextProps) =>
      prevProps.typeEvenementId === nextProps.typeEvenementId &&
      JSON.stringify(prevProps.typeEvenement) === JSON.stringify(nextProps.typeEvenement),
);
