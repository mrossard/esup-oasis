/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

// --- INVALIDATION ---
import { QueryClient } from "@tanstack/react-query";

export type UseInvalidationHook = (queryKeys: string[], onSuccess?: VoidFunction) => void;

export function handleInvalidation(
   client: QueryClient,
   queryKeys: string[],
   onSuccess?: VoidFunction,
) {
   client
      .invalidateQueries({
         predicate: (query) =>
            query.queryKey !== undefined &&
            query.queryKey["0"] !== undefined &&
            query.queryKey["0"] !== null &&
            queryKeys.some((qk) => {
               return (query.queryKey[0] as string).startsWith(qk);
            }),
      })
      .then(() => onSuccess && onSuccess());
}
