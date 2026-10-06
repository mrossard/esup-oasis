/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 *
 */

import React, { useMemo, useState } from "react";
import { Button, Checkbox, Divider, Dropdown, Flex, Space, theme, Typography } from "antd";
import { HolderOutlined, TableOutlined } from "@ant-design/icons";
import {
  BENEFICIAIRE_TABLE_COLUMNS_KEYS,
  TableColumnOption,
} from "@controls/Table/BeneficiaireTableColumns";

export interface BeneficiaireTableColumnsDropdownProps {
  colonnesDisponibles: TableColumnOption[];
  colonnesVisibles: string[];
  onChangeColonnesVisibles: (colonnes: string[]) => void;
  onReorderColonnes?: (colonnesOrdre: string[]) => void;
  onReset?: () => void;
  colonnesComplementaires?: string[];
  className?: string;
}

export function BeneficiaireTableColumnsDropdown({
  colonnesDisponibles,
  colonnesVisibles,
  onChangeColonnesVisibles,
  onReorderColonnes,
  onReset,
  colonnesComplementaires,
  className,
}: BeneficiaireTableColumnsDropdownProps) {
  const [open, setOpen] = useState(false);
  const [draggedKey, setDraggedKey] = useState<string | null>(null);
  const [dragOverKey, setDragOverKey] = useState<string | null>(null);
  const { token } = theme.useToken();

  const complementarySet = useMemo(() => {
    if (colonnesComplementaires !== undefined) {
      return new Set(colonnesComplementaires);
    }
    const standardKeys = new Set<string>(Object.values(BENEFICIAIRE_TABLE_COLUMNS_KEYS));
    return new Set(colonnesDisponibles.map((c) => c.key).filter((key) => !standardKeys.has(key)));
  }, [colonnesComplementaires, colonnesDisponibles]);

  const handleReorder = (fromKey: string, toKey: string) => {
    const currentKeys = colonnesDisponibles.map((c) => c.key);
    const fromIndex = currentKeys.indexOf(fromKey);
    const toIndex = currentKeys.indexOf(toKey);

    if (fromIndex === -1 || toIndex === -1 || fromIndex === toIndex) return;

    const newKeys = [...currentKeys];
    const [movedKey] = newKeys.splice(fromIndex, 1);

    let targetIndex: number;
    if (toKey === BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM) {
      targetIndex = 1;
    } else if (toKey === BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS) {
      targetIndex = newKeys.indexOf(BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS);
      if (targetIndex === -1) targetIndex = newKeys.length;
    } else {
      targetIndex = newKeys.indexOf(toKey);
      if (fromIndex < toIndex) {
        targetIndex += 1;
      }
    }

    newKeys.splice(targetIndex, 0, movedKey);

    // Bénéficiaire est toujours en 1ère position
    const nomIndex = newKeys.indexOf(BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM);
    if (nomIndex > 0) {
      newKeys.splice(nomIndex, 1);
      newKeys.unshift(BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM);
    }

    // Actions n'est pas déplaçable et reste en dernière position
    const actionsIndex = newKeys.indexOf(BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS);
    if (actionsIndex !== -1 && actionsIndex !== newKeys.length - 1) {
      newKeys.splice(actionsIndex, 1);
      newKeys.push(BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS);
    }

    onReorderColonnes?.(newKeys);
  };

  return (
    <Dropdown
      trigger={["click"]}
      open={open}
      onOpenChange={setOpen}
      transitionName=""
      popupRender={() => (
        <div
          style={{
            backgroundColor: token.colorBgElevated,
            borderRadius: token.borderRadiusLG,
            boxShadow: token.boxShadowSecondary,
            padding: 12,
            minWidth: 260,
          }}
        >
          <Flex justify="space-between" align="center" className="mb-1 pb-1 border-bottom">
            <Typography.Text strong className="fs-09">
              Colonnes
            </Typography.Text>
            <Space size="middle">
              {onReset && (
                <Button type="link" size="small" className="p-0 fs-08" onClick={onReset}>
                  Réinitialiser
                </Button>
              )}
              <Button
                type="link"
                size="small"
                className="p-0 fs-08"
                onClick={() => onChangeColonnesVisibles(colonnesDisponibles.map((c) => c.key))}
              >
                Tout afficher
              </Button>
            </Space>
          </Flex>
          <Flex vertical gap={2}>
            {colonnesDisponibles.map((col, index) => {
              const isNom = col.key === BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM;
              const isActions = col.key === BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS;
              const isDraggable = !isNom && !isActions;
              const isChecked = isNom || colonnesVisibles.includes(col.key);

              const isComplementary = complementarySet.has(col.key);
              const prevCol = index > 0 ? colonnesDisponibles[index - 1] : undefined;
              const prevIsComplementary = prevCol ? complementarySet.has(prevCol.key) : false;

              const showDividerBefore =
                index > 0 &&
                (isActions ||
                  (isComplementary && !prevIsComplementary) ||
                  (!isComplementary && prevIsComplementary));

              return (
                <React.Fragment key={col.key}>
                  {showDividerBefore && (
                    <Divider
                      data-testid={
                        isActions
                          ? "divider-actions"
                          : isComplementary
                            ? "divider-before-infos-complementaires"
                            : "divider-after-infos-complementaires"
                      }
                      style={{ margin: "4px 0" }}
                    />
                  )}
                  <div
                    data-testid={`column-item-${col.key}`}
                    draggable={isDraggable}
                    onDragStart={(e) => {
                      if (!isDraggable) return;
                      if (e.dataTransfer) {
                        e.dataTransfer.setData("text/plain", col.key);
                        e.dataTransfer.effectAllowed = "move";
                      }
                      setDraggedKey(col.key);
                    }}
                    onDragOver={(e) => {
                      if (!draggedKey || draggedKey === col.key) return;
                      e.preventDefault();
                      if (e.dataTransfer) {
                        e.dataTransfer.dropEffect = "move";
                      }
                      if (dragOverKey !== col.key) {
                        setDragOverKey(col.key);
                      }
                    }}
                    onDragLeave={() => {
                      if (dragOverKey === col.key) {
                        setDragOverKey(null);
                      }
                    }}
                    onDrop={(e) => {
                      e.preventDefault();
                      if (draggedKey && draggedKey !== col.key) {
                        handleReorder(draggedKey, col.key);
                      }
                      setDraggedKey(null);
                      setDragOverKey(null);
                    }}
                    onDragEnd={() => {
                      setDraggedKey(null);
                      setDragOverKey(null);
                    }}
                    style={{
                      opacity: draggedKey === col.key ? 0.35 : 1,
                      backgroundColor:
                        dragOverKey === col.key && draggedKey !== col.key
                          ? token.colorFillAlter
                          : undefined,
                      borderTop:
                        dragOverKey === col.key && draggedKey !== col.key
                          ? `2px solid ${token.colorPrimary}`
                          : "2px solid transparent",
                      borderBottom: "2px solid transparent",
                      borderRadius: token.borderRadiusSM,
                      padding: "3px 4px",
                      transition: "border-color 0.15s ease, background-color 0.15s ease",
                      userSelect: "none",
                    }}
                  >
                    <Flex justify="space-between" align="center" className="w-100">
                      <Checkbox
                        checked={isChecked}
                        disabled={isNom}
                        onChange={(e) => {
                          if (isNom) return;
                          if (e.target.checked) {
                            onChangeColonnesVisibles([...colonnesVisibles, col.key]);
                          } else {
                            onChangeColonnesVisibles(colonnesVisibles.filter((k) => k !== col.key));
                          }
                        }}
                      >
                        {col.label}
                      </Checkbox>
                      {isDraggable && (
                        <HolderOutlined
                          style={{
                            cursor: draggedKey === col.key ? "grabbing" : "grab",
                            color: token.colorTextDescription,
                            fontSize: 14,
                            padding: "2px 4px",
                          }}
                          aria-label={`Déplacer la colonne ${col.label}`}
                          title="Glisser pour réorganiser"
                        />
                      )}
                    </Flex>
                  </div>
                </React.Fragment>
              );
            })}
          </Flex>
        </div>
      )}
    >
      <Button className={className} icon={<TableOutlined />}>
        Colonnes
      </Button>
    </Dropdown>
  );
}
