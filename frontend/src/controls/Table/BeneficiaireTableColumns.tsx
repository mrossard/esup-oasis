/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 *
 */

import { ColumnType } from "antd/es/table";
import { IBeneficiaire, IDecisionEtablissement, IUtilisateur } from "@api";
import { BeneficiaireProfilItem } from "@controls/Items/BeneficiaireProfilItem";
import { ChargesAccompagnementsItem } from "@controls/Items/ChargesAccompagnementsItem";
import { ComposanteItem } from "@controls/Items/ComposanteItem";
import React from "react";
import { Button, Popconfirm, Space, Tooltip, Typography } from "antd";
import Icon, { EyeOutlined, MinusOutlined, UserSwitchOutlined } from "@ant-design/icons";
import { ascToAscend } from "@utils/array";
import { FilterProps } from "@utils/table";
import { FiltreBeneficiaire } from "@controls/Table/BeneficiaireTable";
import { RoleValues, Utilisateur } from "@lib";
import ExternalLink from "@/assets/images/external-link.svg?react";

import { UtilisateurTag } from "@controls/Tags/UtilisateurTag";
import {
  BeneficiaireAvisEseAvatar,
  EtatAvisEse,
} from "@controls/Avatars/BeneficiaireAvisEseAvatar";
import UtilisateurAvatarImage from "@controls/Avatars/UtilisateurAvatarImage";
import { EllipsisMiddle } from "@controls/Typography/EllipsisMiddle";
import Highlighter from "react-highlight-words";
import { removeAccents } from "@utils/string";
import { DecisionEtablissementAvatar } from "@controls/Avatars/DecisionEtablissementAvatar";

import { UseStateDispatch } from "@utils/utils";
import { env } from "@/env";
import dayjs from "dayjs";

export interface TableColumnOption {
  key: string;
  label: string;
}

export const BENEFICIAIRE_TABLE_COLUMNS_KEYS = {
  NOM: "nom",
  COMPOSANTE: "composantes",
  INSCRIPTION: "inscription",
  TAGS: "tags",
  PROFILS: "profils",
  ETAT_AVIS_ESE: "etatAvisEse",
  DECISION_ETAB: "IDecisionEtablissement",
  GESTIONNAIRE: "gestionnaire[]",
  EMAIL: "email",
  NUM_ETUDIANT: "numeroEtudiant",
  STATUT: "statut",
  ACTIONS: "actions",
} as const;

export function getBeneficiaireTableColumnOptions(
  userOrIsGestionnaire?: Utilisateur | boolean,
): TableColumnOption[] {
  const isGestionnaire =
    typeof userOrIsGestionnaire === "boolean"
      ? userOrIsGestionnaire
      : userOrIsGestionnaire?.isGestionnaire;

  const options: TableColumnOption[] = [
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM, label: "Bénéficiaire" },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.COMPOSANTE, label: "Composantes" },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.INSCRIPTION, label: "Inscription" },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.TAGS, label: "Tags" },
  ];

  if (isGestionnaire) {
    options.push({
      key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.PROFILS,
      label: "Profils actifs",
    });
    options.push({
      key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.ETAT_AVIS_ESE,
      label: `Avis ${env.REACT_APP_ESPACE_SANTE_ABV || "santé"}`,
    });
  }

  options.push(
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.DECISION_ETAB, label: "Décision étab." },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.GESTIONNAIRE, label: "Chargé•es d'acc." },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.EMAIL, label: "Email" },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.NUM_ETUDIANT, label: "Numéro étudiant" },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.STATUT, label: "Statut" },
    { key: BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS, label: "Actions" },
  );

  return options;
}

export function getBeneficiaireTableInitialColumns(
  userOrIsGestionnaire?: Utilisateur | boolean,
): string[] {
  const isGestionnaire =
    typeof userOrIsGestionnaire === "boolean"
      ? userOrIsGestionnaire
      : userOrIsGestionnaire?.isGestionnaire;

  const colonnes: string[] = [
    BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM,
    BENEFICIAIRE_TABLE_COLUMNS_KEYS.INSCRIPTION,
    BENEFICIAIRE_TABLE_COLUMNS_KEYS.TAGS,
  ];

  if (isGestionnaire) {
    colonnes.push(
      BENEFICIAIRE_TABLE_COLUMNS_KEYS.PROFILS,
      BENEFICIAIRE_TABLE_COLUMNS_KEYS.ETAT_AVIS_ESE,
    );
  }

  colonnes.push(
    BENEFICIAIRE_TABLE_COLUMNS_KEYS.DECISION_ETAB,
    BENEFICIAIRE_TABLE_COLUMNS_KEYS.GESTIONNAIRE,
    BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS,
  );

  return colonnes;
}

export function getBeneficiaireTableDefaultColumns(
  userOrIsGestionnaire?: Utilisateur | boolean,
): string[] {
  return getBeneficiaireTableInitialColumns(userOrIsGestionnaire);
}

interface TableBeneficiairesColumnsProps {
  user: Utilisateur | undefined;
  filter: FiltreBeneficiaire;
  setFilter: UseStateDispatch<FiltreBeneficiaire>;
  onBeneficiaireSelected: (beneficiaire: IBeneficiaire) => void;
  onImpersonate: (uid: string) => void;
  colonnesVisibles?: string[];
}

export function beneficiaireTableColumns({
  user,
  filter,
  setFilter,
  onBeneficiaireSelected,
  onImpersonate,
  colonnesVisibles,
}: TableBeneficiairesColumnsProps): ColumnType<IBeneficiaire>[] {
  function canImpersonate() {
    return user?.isAdmin && env.REACT_APP_ENVIRONMENT !== "production";
  }

  // noinspection JSUnusedGlobalSymbols
  const clickableCell = {
    className: "pointer",
    onCell: (record: IBeneficiaire) => {
      return {
        onClick: () => {
          onBeneficiaireSelected(record);
        },
      };
    },
  };

  const allColumns = [
    {
      title: "Bénéficiaire",
      dataIndex: "nom",
      fixed: "left",
      key: "nom",
      render: (_value, record) => {
        return (
          <Space>
            <UtilisateurAvatarImage
              as="img"
              utilisateur={record as IUtilisateur}
              width={48}
              size={48}
              role={RoleValues.ROLE_BENEFICIAIRE}
              className="border-0"
              responsive="lg"
            />
            <span>
              <span className="semi-bold">
                <Highlighter
                  textToHighlight={(record?.nom || "").toLocaleUpperCase()}
                  searchWords={[removeAccents(filter.nom || "")]}
                />
              </span>{" "}
              <span className="light">{record?.prenom}</span>
            </span>
          </Space>
        );
      },
      sortDirections: ["ascend", "descend"],
      sorter: true,
      defaultSortOrder: "ascend",
      sortOrder: ascToAscend(filter?.["order[nom]"]),
      ...FilterProps<FiltreBeneficiaire>("nom", filter, setFilter),
      filteredValue: filter?.nom ? [filter?.nom] : null,
      ...clickableCell,
    } as ColumnType<IBeneficiaire>,
    {
      title: "Composantes",
      dataIndex: "composantes",
      key: "composantes",
      render: (_value: string, record: IBeneficiaire) => {
        const inscriptionsActives = record.inscriptions?.filter((inscription) =>
          dayjs(inscription.fin).isAfter(),
        );
        return inscriptionsActives && inscriptionsActives.length > 0 ? (
          <Space className="mt-05 mb-05" orientation="vertical" size={2}>
            {inscriptionsActives.map((inscription) => (
              <ComposanteItem
                key={inscription["@id"]}
                composanteId={inscription?.formation?.composante}
              />
            ))}
          </Space>
        ) : (
          <MinusOutlined />
        );
      },
    },
    {
      title: "Inscription",
      dataIndex: "inscription",
      key: "inscription",
      render: (_value: string, record: IBeneficiaire) => {
        return record.inscriptions ? (
          record.inscriptions
            .filter((inscription) => dayjs(inscription.fin).isAfter())
            .map((inscription) => (
              <Space
                key={inscription["@id"]}
                className="mt-05 mb-05"
                orientation="vertical"
                size={2}
              >
                <ComposanteItem composanteId={inscription?.formation?.composante} />
                <EllipsisMiddle
                  className="light"
                  style={{ maxWidth: 350 }}
                  suffixCount={12}
                  content={inscription?.formation?.libelle as string}
                  expandable
                />
              </Space>
            ))
        ) : (
          <MinusOutlined />
        );
      },
    },
    {
      title: "Tags",
      dataIndex: "tags",
      key: "tags",
      render: (_value: string[], record: IBeneficiaire) => {
        return (
          <div>
            {record.tags?.map((tag) => (
              <UtilisateurTag
                tagId={tag}
                key={tag}
                utilisateurId={record.uid as string}
                className="mr-05"
              />
            ))}
          </div>
        );
      },
    },
    user?.isGestionnaire
      ? {
          title: "Profils actifs",
          dataIndex: "profils",
          key: "profils",
          render: (values: string[]) => {
            return values.map((profilBeneficiaire) => (
              <BeneficiaireProfilItem
                key={profilBeneficiaire}
                profilBeneficiaire={profilBeneficiaire}
                masquerSiInactif
              />
            ));
          },
        }
      : null,
    user?.isGestionnaire
      ? {
          title: `Avis ${env.REACT_APP_ESPACE_SANTE_ABV || "santé"}`,
          dataIndex: "etatAvisEse",
          key: "etatAvisEse",
          className: "text-center",
          render: (value: string) => {
            return (
              <BeneficiaireAvisEseAvatar
                etatAvisEse={value as EtatAvisEse}
                showLabel={false}
                direction="vertical"
                className="fs-12"
              />
            );
          },
        }
      : null,
    {
      title: "Décision étab.",
      dataIndex: "IDecisionEtablissement",
      key: "IDecisionEtablissement",
      className: "text-center",
      render: (_value: IDecisionEtablissement, record: IBeneficiaire) => {
        return record.decisionAmenagementAnneeEnCours ? (
          <DecisionEtablissementAvatar
            decisionEtab={record.decisionAmenagementAnneeEnCours}
            showLabel={false}
            direction="vertical"
            className="fs-12"
          />
        ) : null;
      },
    },
    {
      title: <span aria-label="Chargés d'accompagnement">Chargé•es d'acc.</span>,
      dataIndex: "gestionnaire[]",
      key: "gestionnaire[]",
      render: (_value: string[], record: IBeneficiaire) => {
        return <ChargesAccompagnementsItem utilisateur={record} />;
      },
    },
    {
      title: "Email",
      dataIndex: "email",
      key: "email",
      width: 200,
      render: (_value: string, record: IBeneficiaire) => {
        return record.email ? (
          <div style={{ width: 200 }}>
            <Typography.Text copyable={{ text: record.email }}>{record.email}</Typography.Text>
          </div>
        ) : (
          <MinusOutlined />
        );
      },
    },
    {
      title: "Numéro étudiant",
      dataIndex: "numeroEtudiant",
      key: "numeroEtudiant",
      render: (_value: string, record: IBeneficiaire) => {
        return record.numeroEtudiant ? (
          <Typography.Text
            copyable={{ text: String(record.numeroEtudiant) }}
            style={{ textWrap: "nowrap" }}
          >
            {record.numeroEtudiant}
          </Typography.Text>
        ) : (
          <MinusOutlined />
        );
      },
    },
    {
      title: "Statut",
      dataIndex: "statut",
      key: "statut",
      render: (_value: string, record: IBeneficiaire) => {
        return record.statutEtudiant || <MinusOutlined />;
      },
    },
    {
      key: "actions",
      filteredValue: null,
      title: <span className="sr-only">Actions</span>,
      className: "text-right",
      width: 160,
      render: (_value: unknown, record: IBeneficiaire) => (
        <Space>
          {canImpersonate() && (
            <Popconfirm
              title="Êtes-vous sûr de vouloir prendre l'identité de cet utilisateur ?"
              onConfirm={() => {
                onImpersonate(record.uid as string);
              }}
              okText="Oui"
              cancelText="Non"
              placement="left"
            >
              <Tooltip title="Prendre l'identité">
                <Button icon={<UserSwitchOutlined />} onClick={(ev) => ev.stopPropagation()} />
              </Tooltip>
            </Popconfirm>
          )}
          <Space.Compact>
            <Button
              icon={<EyeOutlined />}
              onClick={() => {
                onBeneficiaireSelected(record);
              }}
            >
              Voir
            </Button>
            {user?.isGestionnaire && (
              <Tooltip title="Ouvrir dans un nouvel onglet">
                <Button
                  className="text-light"
                  icon={<Icon component={ExternalLink} className="fs-08" />}
                  onClick={() => {
                    window.open(`/beneficiaires/${record.uid}`, "_blank");
                  }}
                />
              </Tooltip>
            )}
          </Space.Compact>
        </Space>
      ),
    },
  ].filter((c) => c !== null) as ColumnType<IBeneficiaire>[];

  if (colonnesVisibles) {
    const colMap = new Map<string, ColumnType<IBeneficiaire>>();
    allColumns.forEach((c) => {
      if (c.key) colMap.set(c.key as string, c);
    });

    return colonnesVisibles
      .map((key) => colMap.get(key))
      .filter((c): c is ColumnType<IBeneficiaire> => c !== undefined);
  }

  return allColumns;
}
