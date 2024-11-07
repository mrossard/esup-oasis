/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { useApi } from "../../../context/api/ApiProvider";
import { NB_MAX_ITEMS_PER_PAGE } from "../../../constants";
import { Button, Table } from "antd";
import { EditOutlined } from "@ant-design/icons";
import React from "react";
import { ICharte } from "../../../api/ApiTypeHelpers";
import ProfilItem from "../../Items/ProfilItem";

interface ChartesTableProps {
   editedItem?: ICharte;
   onEdit: (item: ICharte) => void;
}

export function ChartesTable({ editedItem, onEdit }: ChartesTableProps) {
   const { data: Chartes, isFetching } = useApi().useGetCollectionPaginated({
      path: "/chartes",
      page: 1,
      itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
   });

   return (
      <Table<ICharte>
         rowKey={(record) => record["@id"] as string}
         className="table-responsive"
         loading={isFetching}
         rowClassName={(record) => {
            return record["@id"] === editedItem?.["@id"]
               ? "bg-primary-light shadow-1 border-0"
               : "";
         }}
         columns={[
            {
               title: "Libellé",
               dataIndex: "libelle",
               key: "libelle",
            },
            {
               title: "Profils associés",
               dataIndex: "profilsAssocies",
               responsive: ["lg"],
               key: "profilsAssocies",
               render: (value, record) => <ProfilItem profils={record.profilsAssocies} />,
            },
            {
               key: "actions",
               className: "text-right commandes",
               width: 150,
               render: (_, record) => (
                  <Button
                     icon={<EditOutlined />}
                     onClick={() => {
                        onEdit(record);
                     }}
                  >
                     Éditer
                  </Button>
               ),
            },
         ]}
         dataSource={[...(Chartes?.items || [])]}
         pagination={false}
      />
   );
}
