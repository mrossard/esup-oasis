/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

// --- DELETE ITEM ---
import { QueryClient, useMutation, UseMutationResult } from "@tanstack/react-query";
import { handleApiResponse } from "./HandleApiResponse";
import { handleInvalidation } from "./HandleInvalidation";
import { MutationDeleteParams, RequestMethod } from "../ApiProvider";
import { useNavigate } from "react-router-dom";
import { ApiPathMethodResponse, Path } from "../../../api/SchemaHelpers";
import { useAuth } from "../../../auth/AuthProvider";

export type UseDeleteHook = <P extends Path>(options: {
   path: P;
   invalidationQueryKeys?: string[];
   onSuccess?: (variables: MutationDeleteParams) => void;
}) => UseMutationResult<ApiPathMethodResponse<P, "delete">, unknown, MutationDeleteParams>;

export function useDelete<P extends Path>(
   baseUrl: string,
   fetchOptions: RequestInit,
   client: QueryClient,
   options: {
      path: P;
      invalidationQueryKeys?: string[];
      onSuccess?: (variables: MutationDeleteParams) => void;
   },
): UseMutationResult<ApiPathMethodResponse<P, "delete">, unknown, MutationDeleteParams> {
   const navigate = useNavigate();
   const auth = useAuth();
   return useMutation({
      mutationFn: async (params: MutationDeleteParams) => {
         const url = new URL(params["@id"], baseUrl);
         return handleApiResponse(
            RequestMethod.DELETE,
            await fetch(url, {
               ...fetchOptions,
               method: "DELETE",
            }),
            navigate,
            auth,
            options,
         );
      },
      onSuccess: (variables) => {
         if (options.invalidationQueryKeys)
            handleInvalidation(client, options.invalidationQueryKeys);
         if (options.onSuccess) options.onSuccess(variables);
      },
      onError: (error) => {
         console.error(error);
      },
   });
}
