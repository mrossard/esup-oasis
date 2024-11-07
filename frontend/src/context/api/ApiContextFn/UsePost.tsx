/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

// --- POST ITEM ---
import { QueryClient, useMutation, UseMutationResult } from "@tanstack/react-query";
import { handleApiResponse } from "./HandleApiResponse";
import { handleInvalidation } from "./HandleInvalidation";
import { MutationPostParams, RequestMethod } from "../ApiProvider";
import { useNavigate } from "react-router-dom";
import { ApiPathMethodParameters, ApiPathMethodResponse, Path } from "../../../api/SchemaHelpers";
import { buildUrl } from "./UrlBuilder";
import { useAuth } from "../../../auth/AuthProvider";

export type UsePostHook = <P extends Path>(options: {
   path: P;
   url?: string;
   invalidationQueryKeys?: string[];
   onSuccess?: (data: ApiPathMethodResponse<P, "post">, variables: MutationPostParams<P>) => void;
   onError?: () => void;
   parameters?: ApiPathMethodParameters<P, "post">;
}) => UseMutationResult<ApiPathMethodResponse<P, "post">, unknown, MutationPostParams<P>>;

export function usePost<P extends Path>(
   baseUrl: string,
   fetchOptions: RequestInit,
   client: QueryClient,
   options: {
      path: P;
      url?: string;
      invalidationQueryKeys?: string[];
      onSuccess?: (
         data: ApiPathMethodResponse<P, "post">,
         variables: MutationPostParams<P>,
      ) => void;
      onError?: () => void;
      parameters?: ApiPathMethodParameters<P, "post">;
   },
): UseMutationResult<ApiPathMethodResponse<P, "post">, unknown, MutationPostParams<P>> {
   const navigate = useNavigate();
   const auth = useAuth();

   return useMutation({
      mutationFn: async (params: MutationPostParams<P>) => {
         const url = buildUrl<P, "post">(baseUrl, options.path, options.url, options.parameters);
         return handleApiResponse(
            RequestMethod.POST,
            await fetch(url, {
               ...fetchOptions,
               method: "POST",
               body: JSON.stringify(params.data),
            }),
            navigate,
            auth,
            options,
            JSON.stringify(params.data, null, 2),
         );
      },
      onSuccess: (data, variables) => {
         if (options.invalidationQueryKeys)
            handleInvalidation(client, options.invalidationQueryKeys);
         if (options.onSuccess) options.onSuccess(data, variables);
      },
      onError: (error) => {
         console.error(error);
         if (options.onError) options.onError();
      },
   });
}
