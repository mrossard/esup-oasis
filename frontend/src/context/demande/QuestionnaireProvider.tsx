/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, {
   createContext,
   ReactElement,
   ReactNode,
   useContext,
   useEffect,
   useState,
} from "react";
import { Form, FormInstance } from "antd";
import dayjs from "dayjs";
import { ICampagneDemande, IDemande, ITypeDemande } from "../../api/ApiTypeHelpers";
import { RoleValues } from "../../lib/Utilisateur";
import { EtatInfo, getEtatDemande } from "../../lib/demande";
import { useAuth } from "../../auth/AuthProvider";
import { useApi } from "../api/ApiProvider";

export enum FONCTIONNALITES {
   DECLARER_RECEPTIONNEE = "DECLARER_RECEPTIONNEE",
   ATTRIBUER_PROFIL = "ATTRIBUER_PROFIL",
   STATUER_ACCOMPAGNEMENT = "STATUER_ACCOMPAGNEMENT",
   DECLARER_CONFORMITE_DEMANDE = "DECLARER_CONFORMITE_DEMANDE",
   MODIFIER_QUESTIONNAIRE = "MODIFIER_QUESTIONNAIRE",
}

const MATRICE_DROITS_ROLES: {
   [role: string]: {
      [fonctionnalite: string]: boolean | ((rolesCommission: string[]) => boolean);
   };
} = {
   [RoleValues.ROLE_GESTIONNAIRE]: {
      [FONCTIONNALITES.DECLARER_RECEPTIONNEE]: true,
      [FONCTIONNALITES.ATTRIBUER_PROFIL]: true,
      [FONCTIONNALITES.DECLARER_CONFORMITE_DEMANDE]: true,
      [FONCTIONNALITES.STATUER_ACCOMPAGNEMENT]: true,
      [FONCTIONNALITES.MODIFIER_QUESTIONNAIRE]: true,
   },
   [RoleValues.ROLE_MEMBRE_COMMISSION]: {
      [FONCTIONNALITES.DECLARER_RECEPTIONNEE]: false,
      [FONCTIONNALITES.ATTRIBUER_PROFIL]: (roles) => roles.includes("ROLE_ATTRIBUER_PROFIL"),
      [FONCTIONNALITES.DECLARER_CONFORMITE_DEMANDE]: (roles) =>
         roles.includes("ROLE_VALIDER_CONFORMITE_DEMANDE"),
      [FONCTIONNALITES.STATUER_ACCOMPAGNEMENT]: false,
      [FONCTIONNALITES.MODIFIER_QUESTIONNAIRE]: false,
   },
   [RoleValues.ROLE_RENFORT]: {
      [FONCTIONNALITES.DECLARER_RECEPTIONNEE]: false,
      [FONCTIONNALITES.ATTRIBUER_PROFIL]: false,
      [FONCTIONNALITES.DECLARER_CONFORMITE_DEMANDE]: true,
      [FONCTIONNALITES.STATUER_ACCOMPAGNEMENT]: false,
      [FONCTIONNALITES.MODIFIER_QUESTIONNAIRE]: true,
   },
};

export type QuestionnaireReponse = {
   optionChoisie?: string[] | null;
   commentaire?: string | null;
   piecesJustificatives?: string[];
};

export type QuestionnaireQuestion = {
   "@id": string;
   libelle: string;
   aide?: string | null;
   typeReponse: string;
   obligatoire: boolean;
   choixMultiple: boolean;
   reponse?: QuestionnaireReponse;
};

export type QuestionnaireEtape = {
   "@id": string;
   libelle?: string;
   ordre?: number;
   questions: QuestionnaireQuestion[] | string[];
};

export type Questionnaire = {
   "@id": string;
   typeDemandeId: string;
   libelle?: string;
   complete: boolean;
   etapes: QuestionnaireEtape[];
};

export interface QuestionnaireContextType {
   typeDemande?: ITypeDemande;
   form?: FormInstance<ITypeDemande>;
   demande?: IDemande;
   etatDemande?: EtatInfo;
   questionnaire?: Questionnaire;
   mode: "preview" | "saisie";
   setMode: (mode: "preview" | "saisie") => void;
   submitting?: boolean;
   setSubmitting: (submitting: boolean) => void;
   campagne?: ICampagneDemande;
   blocage?: boolean;
   setBlocage: (blocage: boolean) => void;
   questUtils?: {
      isGrantedQuestionnaire: (
         fonctionnalite: FONCTIONNALITES,
         rolesCommission?: string[],
      ) => boolean;
      envoyerReponse: (
         questionId: string,
         type: string,
         value: string | string[] | undefined,
         onSuccess?: () => void,
         onError?: (error: unknown) => void,
      ) => void;
      getReponseValue: (
         type: string,
         value: string | string[] | null | undefined,
         commentaire: string | null | undefined,
      ) => string | string[] | boolean | dayjs.Dayjs | null | undefined;
      getFormInitialValues: () =>
         | undefined
         | FlatArray<
         {
            [p: string]: string | string[] | null | undefined | dayjs.Dayjs | boolean;
         }[][],
         1
      >;
   };
}

// Create a context for the Questionnaire with a default value of null.
const QuestionnaireContext = createContext<QuestionnaireContextType>({
   mode: "saisie",
   setMode: () => {
   },
   setSubmitting: () => {
   },
   blocage: false,
   setBlocage: () => {
   },
});

/**
 * Create a questionnaire from a demande.
 * @param demande
 * @param typeDemande
 */
function questionnaireFromDemande(demande: IDemande, typeDemande: ITypeDemande): Questionnaire {
   return {
      "@id": demande["@id"] as string,
      typeDemandeId: demande.typeDemande as string,
      libelle: typeDemande.libelle,
      complete: demande.complete || false,
      etapes: (demande.etapes || [])?.map((etape, index) => {
         return {
            "@id": etape.etape as string,
            libelle: etape.libelle,
            ordre: index,
            questions: (etape.questions || []).map((question) => ({
               "@id": question.question as string,
               libelle: question.libelle as string,
               aide: question.aide,
               typeReponse: question.typeReponse as string,
               obligatoire: question.obligatoire ?? false,
               choixMultiple: question.choixMultiple ?? false,
               reponse: {
                  optionChoisie: question.reponse?.optionsReponses?.map(
                     (option) => option["@id"] as string,
                  ),
                  commentaire: question.reponse?.commentaire,
                  piecesJustificatives: question.reponse?.piecesJustificatives,
               },
            })),
         };
      }),
   };
}

/**
 * Create a questionnaire from a type demande.
 * @param typeDemande
 */
function questionnaireFromTypeDemande(typeDemande: ITypeDemande): Questionnaire {
   return {
      "@id": typeDemande["@id"] as string,
      typeDemandeId: typeDemande["@id"] as string,
      libelle: typeDemande.libelle,
      complete: false,
      etapes: (typeDemande.etapes || [])?.map((td, index) => {
         return {
            "@id": td["@id"] as string,
            libelle: td.libelle,
            ordre: index,
            questions: td.questions,
         } as QuestionnaireEtape;
      }),
   };
}

/**
 * Get the value of a response.
 * @param type
 * @param value
 * @param commentaire
 * @param pieceJustificative
 */
function getReponseValue(
   type: string,
   value: string | string[] | null | undefined,
   commentaire: string | null | undefined,
   pieceJustificative?: string | string[],
) {
   switch (type) {
      case "text":
      case "numeric":
      case "textarea":
         return commentaire;
      case "date":
         return commentaire ? dayjs(commentaire) : null;
      case "checkbox":
      case "select":
         return value;
      case "radio":
         return Array.isArray(value) ? value[0] : value;
      case "file":
         return pieceJustificative;
   }
   return null;
}

/**
 * Provide the questionnaire context to child components.
 *
 * @function QuestionnaireProvider
 * @param {Object} props - The properties for the QuestionnaireProvider component.
 * @returns {ReactElement} The QuestionnaireProvider component.
 */
export function QuestionnaireProvider(props: {
   typeDemandeId?: string;
   demandeId?: string;
   mode?: "preview" | "saisie";
   children: ReactNode;
}): ReactElement {
   const auth = useAuth();

   const [typeDemande, setTypeDemande] = useState<ITypeDemande>();
   const [etatDemande, setEtatDemande] = useState<EtatInfo>();
   const [demande, setDemande] = useState<IDemande>();
   const [questionnaire, setQuestionnaire] = useState<Questionnaire>();
   const [form] = Form.useForm<ITypeDemande>();
   const [mode, setMode] = useState<"preview" | "saisie">(props.mode || "saisie");
   const [blocage, setBlocage] = useState<boolean>(false);
   const [submitting, setSubmitting] = useState<boolean>(false);

   // API : Get the demande data.
   const { data: demandeData, refetch: refetchDemande } = useApi().useGetItem({
      path: "/demandes/{id}",
      url: props.demandeId,
      enabled: !!props.demandeId,
   });

   useEffect(() => {
      if (demandeData) {
         setDemande(demandeData);
      }
   }, [demandeData]);

   // API : Get the typeDemande data.
   const { data: typeDemandeData } = useApi().useGetItem({
      path: "/types_demandes/{id}",
      url: props.typeDemandeId || demandeData?.typeDemande,
      enabled: !!props.typeDemandeId || !!demandeData?.typeDemande,
   });

   useEffect(() => {
      if (typeDemandeData) {
         setTypeDemande(typeDemandeData);
      }
   }, [typeDemandeData]);

   // API : Get the campagne data.
   const { data: campagne } = useApi().useGetItem({
      path: "/types_demandes/{typeId}/campagnes/{id}",
      url: demande?.campagne,
      enabled: !!demande?.campagne,
   });

   useEffect(() => {
      if (typeDemandeData) {
         setTypeDemande(typeDemandeData);
      }
   }, [typeDemandeData]);

   // Build questionnaire
   useEffect(() => {
      if (props.demandeId && demande && typeDemande) {
         // Pour gestionnaire + demandeur
         setQuestionnaire(questionnaireFromDemande(demande, typeDemande));
      } else if (props.typeDemandeId && typeDemande && !props.demandeId) {
         // Pour l'admin
         setQuestionnaire(questionnaireFromTypeDemande(typeDemande));
      }
   }, [demande, props, typeDemande]);

   // Send reponse to API
   const mutationReponse = useApi().usePut({
      path: "/demandes/{demandeId}/questions/{questionId}/reponse",
      onSuccess: () => {
         setBlocage(false);
         refetchDemande().then();
      },
   });

   // Etat de la demande
   useEffect(() => {
      if (demande) {
         setEtatDemande(getEtatDemande(demande.etat as string));
      } else {
         setEtatDemande(undefined);
      }
   }, [demande]);

   function isGrantedQuestionnaire(
      fonctionnalite: FONCTIONNALITES,
      rolesCommission?: string[],
   ): boolean {
      if (!auth.user) return false;
      for (const role of auth.user.roles) {
         const droits = MATRICE_DROITS_ROLES[role];
         if (droits && droits[fonctionnalite] && typeof droits[fonctionnalite] === "boolean") {
            return droits[fonctionnalite] as boolean;
         } else if (droits && droits[fonctionnalite]) {
            return (droits[fonctionnalite] as (r: string[]) => boolean)(rolesCommission || []);
         }
      }

      return false;
   }

   /**
    * Send a response to the API.
    * @param questionId
    * @param type
    * @param value
    * @param onSuccess
    * @param onError
    */
   function envoyerReponse(
      questionId: string,
      type: string,
      value: string | string[] | undefined,
      onSuccess?: () => void,
      onError?: (error: unknown) => void,
   ) {
      if (!demande) return;

      setSubmitting(true);
      let valueToSend: string[] = [];
      let commentaireToSend = null;
      let pieceJustificativeToSend: string[] | undefined = undefined;

      switch (type) {
         case "text":
         case "textarea":
         case "date":
         case "submit":
            valueToSend = [];
            commentaireToSend = Array.isArray(value) ? value[0] : value;
            break;
         case "numeric":
            if (isNaN(Number(value))) {
               return;
            } else {
               valueToSend = [];
               commentaireToSend = Array.isArray(value) ? value[0] : value;
            }
            break;
         case "radio":
         case "checkbox":
         case "select":
            valueToSend = Array.isArray(value) ? value : ((value ? [value] : []) as string[]);
            commentaireToSend = null;
            break;
         case "file":
            pieceJustificativeToSend = Array.isArray(value)
               ? value
               : ((value ? [value] : []) as string[]);
      }

      mutationReponse.mutate(
         {
            "@id": `${demande["@id"] + questionId}/reponse` as string,
            data: {
               optionsChoisies: valueToSend,
               commentaire: commentaireToSend,
               piecesJustificatives: pieceJustificativeToSend,
            },
         },
         {
            onSuccess: () => {
               onSuccess?.();
               setSubmitting(false);
            },
            onError: (error: unknown) => {
               onError?.(error);
               setSubmitting(false);
            },
         },
      );
   }

   /**
    * Get the initial values for the form.
    */
   function getFormInitialValues():
      | undefined
      | FlatArray<
      {
         [p: string]: string | string[] | null | undefined | dayjs.Dayjs | boolean;
      }[][],
      1
   > {
      if (!questionnaire) return undefined;
      return questionnaire.etapes
         .map((etape: QuestionnaireEtape) => {
            return etape.questions.map((question) => {
               // si question est de type string
               if (typeof question === "string") {
                  return {};
               }
               return {
                  [question["@id"]]: getReponseValue(
                     question.typeReponse,
                     question.reponse?.optionChoisie,
                     question.reponse?.commentaire,
                     question.reponse?.piecesJustificatives,
                  ),
               };
            });
         })
         .flat()
         .reduce((acc, curr) => ({ ...acc, ...curr }), {});
   }

   // Provide the context to child components.
   return (
      <QuestionnaireContext.Provider
         value={{
            typeDemande,
            form,
            demande,
            questionnaire,
            mode,
            setMode,
            submitting,
            setSubmitting,
            etatDemande,
            campagne,
            blocage,
            setBlocage,
            questUtils: {
               isGrantedQuestionnaire,
               envoyerReponse,
               getReponseValue,
               getFormInitialValues,
            },
         }}
      >
         {props.children}
      </QuestionnaireContext.Provider>
   );
}

/**
 * @function useQuestionnaire
 * @returns {QuestionnaireContextType} The context for the questionnaire.
 */
export function useQuestionnaire(): QuestionnaireContextType {
   // Use the context for the questionnaire.
   return useContext(QuestionnaireContext);
}
