/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 *
 */

import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import {
  BENEFICIAIRE_TABLE_COLUMNS_KEYS,
  getBeneficiaireTableColumnOptions,
  getBeneficiaireTableInitialColumns,
  TableColumnOption,
} from "@controls/Table/BeneficiaireTableColumns";
import { usePreferences } from "@context/utilisateurPreferences/UtilisateurPreferencesProvider";

export const STORAGE_KEY_COLONNES_BENEFICIAIRES = "oasis:table:beneficiaires:colonnes";
export const PREF_KEY_COLONNES_BENEFICIAIRES = "colonnesBeneficiaires";

interface ColumnPreferences {
  ordre: string[];
  visibles: string[];
}

function sanitizeColumnPreferences(
  availableKeys: string[],
  initialKeys: string[],
  storedOrdre?: string[] | null,
  storedVisibles?: string[] | null,
): ColumnPreferences {
  const nom = BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM;
  const actions = BENEFICIAIRE_TABLE_COLUMNS_KEYS.ACTIONS;

  const defaultOrder = [
    ...initialKeys.filter((k) => k !== actions),
    ...availableKeys.filter((k) => !initialKeys.includes(k) && k !== actions),
    ...(availableKeys.includes(actions) ? [actions] : []),
  ];

  const middleKeys = (storedOrdre || defaultOrder).filter(
    (k) => availableKeys.includes(k) && k !== nom && k !== actions,
  );
  availableKeys.forEach((k) => {
    if (k !== nom && k !== actions && !middleKeys.includes(k)) {
      middleKeys.push(k);
    }
  });

  const ordre = [nom, ...middleKeys, ...(availableKeys.includes(actions) ? [actions] : [])];
  const rawVisibles = storedVisibles || initialKeys;
  const visibles = [nom, ...rawVisibles.filter((k) => availableKeys.includes(k) && k !== nom)];

  return { ordre, visibles };
}

function parseStoredPreferences(stored: string | null): ColumnPreferences | null {
  if (!stored) return null;
  try {
    const parsed = JSON.parse(stored);
    if (Array.isArray(parsed) && parsed.length > 0) return { ordre: parsed, visibles: parsed };
    if (parsed && typeof parsed === "object") return parsed;
  } catch {
    /* ignore */
  }
  return null;
}

export function useBeneficiaireTableColumnPrefs(
  isGestionnaire?: boolean,
  colonnesComplementaires?: string[],
) {
  const { getPreferenceJson, setPreferenceJson, preferencesChargees } = usePreferences();

  const initialColumns = useMemo(
    () => getBeneficiaireTableInitialColumns(isGestionnaire),
    [isGestionnaire],
  );

  const colonnesDisponibles = useMemo(
    () => getBeneficiaireTableColumnOptions(isGestionnaire, colonnesComplementaires),
    [isGestionnaire, colonnesComplementaires],
  );

  const availableKeys = useMemo(() => colonnesDisponibles.map((c) => c.key), [colonnesDisponibles]);

  const hadCustomColumns = useRef(false);

  const [columnPrefs, setColumnPrefs] = useState<ColumnPreferences>(() => {
    const stored = parseStoredPreferences(localStorage.getItem(STORAGE_KEY_COLONNES_BENEFICIAIRES));
    if (stored) {
      hadCustomColumns.current = true;
      return sanitizeColumnPreferences(
        availableKeys,
        initialColumns,
        stored.ordre,
        stored.visibles,
      );
    }
    return sanitizeColumnPreferences(availableKeys, initialColumns);
  });

  const serverPrefsLoaded = useRef(false);

  // Synchronisation lors du chargement des préférences utilisateur
  useEffect(() => {
    if (!preferencesChargees || serverPrefsLoaded.current) return;
    serverPrefsLoaded.current = true;

    const serverPref = getPreferenceJson?.(PREF_KEY_COLONNES_BENEFICIAIRES) as
      | ColumnPreferences
      | undefined;

    const hasServerPrefs =
      serverPref && (Array.isArray(serverPref.ordre) || Array.isArray(serverPref.visibles));

    if (hasServerPrefs) {
      hadCustomColumns.current = true;
      const sanitized = sanitizeColumnPreferences(
        availableKeys,
        initialColumns,
        serverPref.ordre,
        serverPref.visibles,
      );
      // eslint-disable-next-line react-hooks/set-state-in-effect
      setColumnPrefs(sanitized);
      try {
        localStorage.setItem(STORAGE_KEY_COLONNES_BENEFICIAIRES, JSON.stringify(sanitized));
      } catch {
        /* ignore */
      }
    } else if (hadCustomColumns.current) {
      setPreferenceJson?.(PREF_KEY_COLONNES_BENEFICIAIRES, columnPrefs);
    }
  }, [
    preferencesChargees,
    availableKeys,
    initialColumns,
    getPreferenceJson,
    setPreferenceJson,
    columnPrefs,
  ]);

  // Si les colonnes disponibles ou initiales changent, synchroniser avec availableKeys
  useEffect(() => {
    setColumnPrefs((prev) => {
      const sanitized = sanitizeColumnPreferences(
        availableKeys,
        initialColumns,
        hadCustomColumns.current ? prev.ordre : null,
        hadCustomColumns.current ? prev.visibles : null,
      );
      if (
        sanitized.ordre.join(",") === prev.ordre.join(",") &&
        sanitized.visibles.join(",") === prev.visibles.join(",")
      ) {
        return prev;
      }
      return sanitized;
    });
  }, [availableKeys, initialColumns]);

  // Persister en localStorage si personnalisé
  useEffect(() => {
    if (hadCustomColumns.current) {
      try {
        localStorage.setItem(STORAGE_KEY_COLONNES_BENEFICIAIRES, JSON.stringify(columnPrefs));
      } catch {
        /* ignore */
      }
    }
  }, [columnPrefs]);

  const updateColumnPrefs = useCallback(
    (newPrefs: ColumnPreferences) => {
      hadCustomColumns.current = true;
      setColumnPrefs(newPrefs);
      try {
        localStorage.setItem(STORAGE_KEY_COLONNES_BENEFICIAIRES, JSON.stringify(newPrefs));
      } catch {
        /* ignore */
      }
      setPreferenceJson?.(PREF_KEY_COLONNES_BENEFICIAIRES, newPrefs);
    },
    [setPreferenceJson],
  );

  const handleChangeColonnesVisibles = useCallback(
    (visibles: string[]) => {
      const nom = BENEFICIAIRE_TABLE_COLUMNS_KEYS.NOM;
      const ensured = visibles.includes(nom) ? visibles : [nom, ...visibles];
      updateColumnPrefs({ ...columnPrefs, visibles: ensured });
    },
    [columnPrefs, updateColumnPrefs],
  );

  const handleReorderColonnes = useCallback(
    (nouvelOrdre: string[]) => {
      updateColumnPrefs({ ...columnPrefs, ordre: nouvelOrdre });
    },
    [columnPrefs, updateColumnPrefs],
  );

  const handleResetColonnes = useCallback(() => {
    const newPrefs = sanitizeColumnPreferences(availableKeys, initialColumns, null, null);
    updateColumnPrefs(newPrefs);
  }, [availableKeys, initialColumns, updateColumnPrefs]);

  const colonnesOrdonnees = useMemo(() => {
    const optionsMap = new Map(colonnesDisponibles.map((c) => [c.key, c]));
    return columnPrefs.ordre
      .map((key) => optionsMap.get(key))
      .filter((c): c is TableColumnOption => c !== undefined);
  }, [colonnesDisponibles, columnPrefs.ordre]);

  const colonnesAffichees = useMemo(() => {
    return columnPrefs.ordre.filter((key) => columnPrefs.visibles.includes(key));
  }, [columnPrefs.ordre, columnPrefs.visibles]);

  return {
    colonnesDisponibles,
    colonnesOrdonnees,
    colonnesAffichees,
    colonnesVisibles: columnPrefs.visibles,
    handleChangeColonnesVisibles,
    handleReorderColonnes,
    handleResetColonnes,
  };
}
