/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useState } from "react";
import { Input } from "antd";

interface ITarifFormItem {
   value?: string;
   onChange?: (v: string) => void;
   style?: React.CSSProperties;
   className?: string;
}

/**
 * Render a TarifFormItem component for event types.
 *
 * @param {Object} params - The parameters object.
 * @param {string} [params.value] - The initial value of the TarifFormItem.
 * @param {function} [params.onChange] - The callback function to be called when the value of the TarifFormItem changes.
 * @param {Object} [params.style] - The CSS style to be applied to the TarifFormItem.
 * @param {string} [params.className] - The CSS class name to be applied to the TarifFormItem.
 * @returns {ReactElement} The rendered TarifFormItem component.
 */
function TarifFormItem({ value, onChange, style, className }: ITarifFormItem): ReactElement {
   const [utilisateur, setUtilisateur] = useState(value);

   const change = (v: string) => {
      setUtilisateur(v);
      if (typeof onChange === "function") {
         onChange(v);
      }
   };

   return (
      <>
         <Input
            style={style}
            className={className}
            suffix="€"
            value={utilisateur}
            onChange={(e) => change(e.target.value)}
         />
      </>
   );
}

export default TarifFormItem;
