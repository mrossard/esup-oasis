/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement, useState } from "react";
import { Breadcrumb, Col, FloatButton, Layout, Row, Space, Typography } from "antd";
import { NavLink } from "react-router-dom";
import { HomeFilled, PlusOutlined } from "@ant-design/icons";
import { PeriodesRhEdition } from "../../../../controls/Admin/Referentiel/PeriodeRh/PeriodesRhEdition";
import { PeriodesRhTable } from "../../../../controls/Table/Admin/PeriodesRhTable";
import { IPeriode } from "../../../../api/ApiTypeHelpers";

/**
 * Renders the Administration page for managing RH periods.
 *
 * @returns {ReactElement} The rendered Administration page.
 */
export default function PeriodesRh(): ReactElement {
   const [editedItem, setEditedItem] = useState<IPeriode | undefined>();

   return (
      <Layout.Content className="administration" style={{ padding: "0 50px" }}>
         <Typography.Title level={1}>Administration</Typography.Title>
         <Breadcrumb
            className="mt-2"
            items={[
               {
                  key: "administration",
                  title: (
                     <NavLink to="/administration">
                        <Space>
                           <HomeFilled />
                           Administration
                        </Space>
                     </NavLink>
                  ),
               },
               {
                  key: "planification",
                  title: (
                     <NavLink to="/administration#planification">
                        <Space>Planification</Space>
                     </NavLink>
                  ),
               },
               {
                  key: "periodes-rh",
                  title: "Périodes RH",
               },
            ]}
         />
         <Typography.Title level={2}>Périodes RH</Typography.Title>
         <Row gutter={[16, 16]}>
            <Col span={24}>
               <PeriodesRhTable onEdit={setEditedItem} />
            </Col>
         </Row>
         <FloatButton
            icon={<PlusOutlined />}
            type="primary"
            tooltip="Ajouter une période"
            onClick={() => {
               setEditedItem({
                  debut: null,
                  fin: null,
                  butoir: null,
               });
            }}
         />
         {editedItem && <PeriodesRhEdition periode={editedItem} setPeriode={setEditedItem} />}
      </Layout.Content>
   );
}
