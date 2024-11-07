/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { IAmenagement, ICategorieAmenagement, ITypeAmenagement } from "../../api/ApiTypeHelpers";
import { useAuth } from "../../auth/AuthProvider";
import { useNavigate } from "react-router-dom";
import { useApi } from "../../context/api/ApiProvider";
import { Table } from "antd";
import { amenagementTableColumns } from "./AmenagementTableColumns";
import { SorterResult } from "antd/es/table/interface";
import React, { useEffect, useState } from "react";
import { FiltreAmenagement, filtreAmenagementToApi } from "./AmenagementTableLayout";
import { ModalAmenagement } from "../Modals/ModalAmenagement";
import { ModeAffichageAmenagement } from "../../routes/gestionnaire/beneficiaires/Amenagements";

export function AmenagementTable(props: {
   filtreAmenagement: FiltreAmenagement;
   setFiltreAmenagement: React.Dispatch<React.SetStateAction<FiltreAmenagement>>;
   typesAmenagements?: ITypeAmenagement[];
   categoriesAmenagements?: ICategorieAmenagement[];
   setCount?: (count: number | undefined) => void;
}) {
   const auth = useAuth();
   const navigate = useNavigate();
   const [editedItem, setEditedItem] = useState<string>();

   const { data: amenagements, isFetching: isFetchingAmenagements } = useApi().useGetCollection({
      path: "/amenagements",
      query: filtreAmenagementToApi(
         props.filtreAmenagement,
         ModeAffichageAmenagement.ParAmenagement,
      ),
   });

   useEffect(() => {
      if (props.setCount) {
         props.setCount(amenagements?.totalItems);
      }
   }, [amenagements, props]);

   // Sticky header
   useEffect(() => {
      function handleScroll() {
         const table = document.querySelector("table") as HTMLElement;
         const tHead = document.querySelector(".ant-table-thead") as HTMLElement;
         tHead.style.top = `${document.documentElement.scrollTop - (table.getBoundingClientRect().top + window.scrollY - 80)}px`;
      }

      window.addEventListener("scroll", handleScroll);
      return () => {
         window.removeEventListener("scroll", handleScroll);
      };
   }, []);

   return (
      <>
         <Table<IAmenagement>
            loading={isFetchingAmenagements}
            dataSource={amenagements?.items || []}
            rowClassName={(_record, index) => (index % 2 === 1 ? "bg-grey-xlight" : "")}
            rowHoverable={false}
            columns={amenagementTableColumns({
               filtre: props.filtreAmenagement,
               setFiltre: props.setFiltreAmenagement,
               typesAmenagements: props.typesAmenagements,
               categoriesAmenagements: props.categoriesAmenagements,
               navigate: navigate,
               isGestionnaire: auth?.user?.isGestionnaire,
               setEditedItem: auth?.user?.isGestionnaire ? undefined : setEditedItem,
            })}
            className="table-responsive table-thead-sticky mt-2"
            pagination={{
               pageSize: props.filtreAmenagement.itemsPerPage,
               total: amenagements?.totalItems,
               current: props.filtreAmenagement.page,
               showTotal: (total, range) => (
                  <div className="text-legende mr-1">
                     {range[0]} à {range[1]} / {total}
                  </div>
               ),
               showSizeChanger: true,
               pageSizeOptions: [25, 50, 100, 200],
            }}
            rowKey={(record) => record["@id"] as string}
            onChange={(
               pagination,
               _filters,
               sorter: SorterResult<IAmenagement> | SorterResult<IAmenagement>[],
            ) => {
               if (Array.isArray(sorter)) {
                  return;
               }
               props.setFiltreAmenagement({
                  ...props.filtreAmenagement,
                  page: pagination.current ?? 1,
                  itemsPerPage: pagination.pageSize ?? 25,
               });
            }}
         />
         {editedItem !== undefined && (
            <ModalAmenagement
               amenagementId={editedItem}
               open={true}
               setOpen={(open) => {
                  if (!open) setEditedItem(undefined);
               }}
            />
         )}
      </>
   );
}
