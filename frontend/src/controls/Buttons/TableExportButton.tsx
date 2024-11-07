/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { Button } from "antd";
import { ExportOutlined } from "@ant-design/icons";
import { CSVLink } from "react-csv";
import { ReactElement, useEffect, useRef } from "react";

import { UseStateDispatch } from "../../utils/utils";
import { env } from "../../env";

interface ExportButtonProps {
   // eslint-disable-next-line @typescript-eslint/no-explicit-any
   getData: () => any[];
   submitted: boolean;
   setSubmitted: UseStateDispatch<boolean>;
   loading: boolean;
   setLoading: UseStateDispatch<boolean>;
   downloaded: boolean;
   setDownloaded: UseStateDispatch<boolean>;
   headers: { label: string; key: string }[];
   filename: string;
   onDownloaded?: () => void;
}

/**
 * Export Button component for exporting table data to CSV format.
 * @param {Object} props - The props object.
 * @param {Function} props.getData - Function that returns the table data.
 * @param {boolean} props.submitted - Flag indicating whether the export button has been submitted.
 * @param {Function} props.setSubmitted - Function to set the submitted flag.
 * @param {boolean} props.loading - Flag indicating whether the data is being prepared for export.
 * @param {Function} props.setLoading - Function to set the loading flag.
 * @param {boolean} props.downloaded - Flag indicating whether the file has been downloaded.
 * @param {Function} props.setDownloaded - Function to set the downloaded flag.
 * @param {Array<string>} props.headers - An array of headers for the CSV file.
 * @param {string} props.filename - The filename of the exported CSV file.
 * @returns {ReactElement} - The Export Button component.
 */
export function TableExportButton({
   getData,
   submitted,
   setSubmitted,
   loading,
   setLoading,
   downloaded,
   setDownloaded,
   headers,
   filename,
   onDownloaded,
}: ExportButtonProps): ReactElement {
   // eslint-disable-next-line @typescript-eslint/no-explicit-any
   const refDownload = useRef<any>(null);

   useEffect(() => {
      if (refDownload.current && !downloaded && !loading && submitted) {
         refDownload.current.link.click();
      }
   });

   if (!submitted) {
      return (
         <Button
            icon={<ExportOutlined />}
            onClick={() => {
               setSubmitted(() => {
                  setLoading(true);
                  return true;
               });
            }}
            loading={loading}
            disabled={submitted}
         >
            Exporter
         </Button>
      );
   }

   if (submitted && loading) {
      return (
         <Button icon={<ExportOutlined />} loading={true} disabled={true}>
            Préparation des données...
         </Button>
      );
   }

   return (
      <CSVLink
         ref={refDownload}
         data={getData()}
         headers={headers}
         filename={`${env.REACT_APP_TITRE}-${filename}.csv`}
         separator=";"
         enclosingCharacter='"'
         onClick={() => {
            setDownloaded(true);
            onDownloaded?.();
         }}
         aria-label="Exporter le tableau au format CSV (Excel)"
      >
         <Button icon={<ExportOutlined />}>Exporter</Button>
      </CSVLink>
   );
}
