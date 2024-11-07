/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { memo, ReactElement } from "react";
import "./Progress.scss";
import { Tooltip } from "antd";
import { LikeOutlined } from "@ant-design/icons";
import { buildStyles, CircularProgressbar } from "react-circular-progressbar";
import useBreakpoint from "antd/es/grid/hooks/useBreakpoint";

interface IProgress {
   value: number | undefined;
   tooltip?: string;
}

/**
 * Renders a progress component.
 *
 * @param {Object} props - The props object.
 * @param {number} [props.value] - The progress value. Accepts values from 0 to 100.
 * @param {string} [props.tooltip] - The tooltip text.
 *
 * @return {ReactElement} The rendered progress component or null if value is undefined.
 */
export default memo(
   function Progress({ value, tooltip }: IProgress): ReactElement {
      const [style, setStyle] = React.useState<React.CSSProperties>({
         color: "transparent",
         fontSize: 0,
      });
      const screens = useBreakpoint();

      const getColor = () => {
         if (value === undefined) return "transparent";
         if (value < 50) return "--color-danger";
         if (value < 100) return "--color-warning";
         return "--color-app";
      };

      const getGradient = () => {
         if (value === undefined) return "transparent";
         return `linear-gradient(to right, var(${getColor()}-light), var(${getColor()}))`;
      };

      setTimeout(() => {
         const newStyle: React.CSSProperties = {
            opacity: 1,
            width: `${value}%`,
            background: getGradient(),
         };

         setStyle(newStyle);
      }, 200);

      if (value === undefined) return <></>;

      if (!screens.xxl) {
         return (
            <Tooltip title={tooltip}>
               <div style={{ width: 30 }}>
                  <CircularProgressbar
                     strokeWidth={50}
                     styles={buildStyles({
                        strokeLinecap: "butt",
                        pathColor: `var(${getColor()})`,
                     })}
                     value={value}
                     text={value === 100 ? "OK" : `${value}%`}
                  />
               </div>
            </Tooltip>
         );
      }

      return (
         <Tooltip title={tooltip}>
            <div className="progress">
               <div className="progress-done" style={style}>
                  {value}%{value === 100 && <LikeOutlined className="ml-1" />}
               </div>
            </div>
         </Tooltip>
      );
   },
   (prevProps, nextProps) =>
      prevProps.value === nextProps.value && prevProps.tooltip === nextProps.tooltip,
);
