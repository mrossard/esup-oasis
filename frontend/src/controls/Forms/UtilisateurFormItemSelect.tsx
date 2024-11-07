/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { useState } from "react";
import { App, Button, Dropdown, Input, Select, Space, Tooltip } from "antd";
import {
   CopyOutlined,
   EnterOutlined,
   MailOutlined,
   SearchOutlined,
   SendOutlined,
   UserOutlined,
} from "@ant-design/icons";
import { useDispatch } from "react-redux";
import { setDrawerUtilisateur } from "../../redux/actions/Drawers";
import { RoleValues } from "../../lib/Utilisateur";
import { useApi } from "../../context/api/ApiProvider";
import Spinner from "../Spinner/Spinner";

import { IUtilisateur } from "../../api/ApiTypeHelpers";

interface IUtilisateurFormItemSelect {
   value?: string;
   onChange?: (v: string | undefined) => void;
   onSelect?: (v: string | undefined) => void;
   style?: React.CSSProperties;
   styleButton?: React.CSSProperties;
   className?: string;
   classNameButton?: string;
   disabled?: boolean;
   placeholder?: string;
   intervenantArchive?: boolean;
   existeNumeroEtudiant?: boolean;
   forcerRechercheEnBase?: boolean;
   roleUtilisateur?:
      | RoleValues.ROLE_INTERVENANT
      | RoleValues.ROLE_BENEFICIAIRE
      | RoleValues.ROLE_RENFORT
      | RoleValues.ROLE_ENSEIGNANT
      | RoleValues.ROLE_REFERENT_COMPOSANTE;
}

export function MailSmallButton(props: {
   utilisateur: IUtilisateur | undefined;
   emailPerso?: boolean;
   className?: string;
   mailto?: boolean;
}) {
   const { message } = App.useApp();
   const email = props.emailPerso ? props.utilisateur?.emailPerso : props.utilisateur?.email;
   if (!email) return null;

   return (
      <Dropdown
         menu={{
            items: [
               {
                  key: "copier",
                  label: "Copier l'adresse email",
                  icon: <CopyOutlined />,
               },
               props.mailto
                  ? {
                     key: "envoyer",
                     label: "Envoyer un email",
                     icon: <SendOutlined />,
                  }
                  : null,
            ],
            onClick: (e) => {
               if (e.key === "copier") {
                  // Copie du mail dans le presse-papier
                  navigator.clipboard.writeText(email).then(() => {
                     message.success("Email copié dans le presse-papier").then();
                  });
               } else if (e.key === "envoyer") {
                  window.open(`mailto:${email}`);
               }
            },
         }}
      >
         <Button
            size="small"
            icon={<MailOutlined />}
            className={`m-0 p-0 border-0 ${props.className || "text-text"}`}
            onClick={() => {
               // Copie du mail dans le presse-papier
               navigator.clipboard.writeText(email).then(() => {
                  message.success("Email copié dans le presse-papier").then();
               });
            }}
         />
      </Dropdown>
   );
}

function getPath(forcerRechercheEnBase = false, roleUtilisateur?: string) {
   if (forcerRechercheEnBase) {
      return "/utilisateurs";
   }

   switch (roleUtilisateur) {
      case RoleValues.ROLE_INTERVENANT:
         return "/intervenants";
      case RoleValues.ROLE_BENEFICIAIRE:
         return "/beneficiaires";
      case RoleValues.ROLE_RENFORT:
         return "/renforts";
      case RoleValues.ROLE_ENSEIGNANT:
      default:
         return "/utilisateurs";
   }
}

function getFiltre(
   forcerRechercheEnBase = false,
   roleUtilisateur?: string,
   recherche?: string,
   intervenantArchive?: boolean,
   existeNumeroEtudiant?: boolean,
) {
   if (forcerRechercheEnBase) {
      return {
         // recherche LDAP
         recherche: recherche,
         intervenantArchive,
         "exists[numeroEtudiant]": existeNumeroEtudiant,
      };
   }

   if (getPath(forcerRechercheEnBase, roleUtilisateur) === "/utilisateurs") {
      return {
         // recherche LDAP
         term: recherche,
         intervenantArchive,
         "exists[numeroEtudiant]": existeNumeroEtudiant,
      };
   } else {
      return {
         // recherche en base
         recherche: recherche,
         intervenantArchive,
         "exists[numeroEtudiant]": existeNumeroEtudiant,
      };
   }
}

function UtilisateurFormItemSelect({
                                      value,
                                      onSelect,
                                      onChange,
                                      style,
                                      styleButton,
                                      className,
                                      classNameButton,
                                      disabled,
                                      placeholder,
                                      roleUtilisateur,
                                      intervenantArchive,
                                      existeNumeroEtudiant,
                                      forcerRechercheEnBase,
                                   }: IUtilisateurFormItemSelect) {
   const [utilisateurId, setUtilisateurId] = useState(value === "" ? undefined : value);
   const [tappedSearch, setTappedSearch] = useState("");
   const [search, setSearch] = useState("");
   const dispatch = useDispatch();
   const { data: dataUtilisateur, isFetching: isFetchingUtilisateur } = useApi().useGetItem({
      path: "/utilisateurs/{uid}",
      url: utilisateurId as string,
      enabled: !!utilisateurId,
   });
   const itemsPerPage = 25;
   const { data: utilisateursTrouves, isFetching: isFetchingUtilisateursTrouves } =
      useApi().useGetCollectionPaginated({
         path: getPath(forcerRechercheEnBase, roleUtilisateur),
         page: 1,
         itemsPerPage: itemsPerPage,
         enabled: search.length > 1,
         query: getFiltre(
            forcerRechercheEnBase,
            roleUtilisateur,
            search,
            intervenantArchive,
            existeNumeroEtudiant,
         ),
      });
   const [isEditing, setIsEditing] = useState(utilisateurId === undefined);
   const [open, setOpen] = useState(false);

   function launchSearch() {
      if (tappedSearch.length > 1) {
         setOpen(true);
         setSearch(tappedSearch);
      }
   }

   function getSuffix() {
      if (isFetchingUtilisateur) return <Spinner />;

      function getUtilisateurColor() {
         switch (roleUtilisateur) {
            case RoleValues.ROLE_BENEFICIAIRE:
               return "bg-beneficiaire border-beneficiaire";
            case RoleValues.ROLE_INTERVENANT:
               return "bg-intervenant border-intervenant";
            default:
               return "";
         }
      }

      return utilisateurId ? (
         <Space>
            <MailSmallButton utilisateur={dataUtilisateur} />
            <Tooltip title="Voir le profil">
               <Button
                  size="small"
                  icon={<UserOutlined />}
                  shape="circle"
                  className={`m-0 p-0 ${classNameButton} text-text ${getUtilisateurColor()}}`}
                  style={styleButton}
                  onClick={() => {
                     dispatch(
                        setDrawerUtilisateur({
                           utilisateur: dataUtilisateur?.["@id"] as string,
                           role: roleUtilisateur,
                        }),
                     );
                  }}
               />
            </Tooltip>
         </Space>
      ) : (
         <Button
            size="small"
            icon={<SearchOutlined />}
            shape="circle"
            className={`m-0 p-0 ${classNameButton}`}
            style={styleButton}
            onClick={() => {
               setIsEditing(true);
            }}
         />
      );
   }

   if (isEditing) {
      return (
         <>
            <Select
               options={utilisateursTrouves?.items
                  .filter((u: IUtilisateur) => {
                     // lorsqu'on force la recherche en base, on en peut pas filtrer sur le rôle de l'utilisateur.
                     // on le fait ici que si le nombre de résultats retournés est !== itemsPerPage
                     // pour ne pas risquer de se retrouver avec une liste vide
                     if (
                        forcerRechercheEnBase &&
                        utilisateursTrouves?.items.length === itemsPerPage
                     ) {
                        return true;
                     }

                     if (forcerRechercheEnBase) {
                        return u.roles?.includes(roleUtilisateur as RoleValues);
                     }

                     return true;
                  })
                  .map((b) => ({
                     value: b["@id"],
                     label: `${b.nom?.toLocaleUpperCase()} ${b.prenom} (${b.email})`,
                  }))}
               loading={isFetchingUtilisateursTrouves}
               showSearch
               filterOption={false}
               className={className}
               style={style}
               open={open && !isFetchingUtilisateursTrouves}
               suffixIcon={
                  tappedSearch !== search && tappedSearch.length > 1 ? (
                     <EnterOutlined
                        className="text-primary"
                        onClick={(e) => {
                           e.preventDefault();
                           launchSearch();
                        }}
                     />
                  ) : undefined
               }
               placeholder={placeholder}
               onSearch={(term) => {
                  setTappedSearch(term);
               }}
               onInputKeyDown={(e) => {
                  if (e.key === "Enter" && tappedSearch.length > 1) {
                     e.preventDefault();
                     launchSearch();
                  }
               }}
               onSelect={(v) => {
                  setOpen(false);
                  setUtilisateurId(v as string);
                  if (onSelect) onSelect(v);
                  setIsEditing(false);
               }}
               onChange={onChange}
            />
         </>
      );
   }

   return (
      <>
         <Input
            style={style}
            className={`mb-0 ${className}`}
            disabled={disabled}
            placeholder={placeholder}
            suffix={getSuffix()}
            value={
               dataUtilisateur
                  ? `${dataUtilisateur?.prenom} ${dataUtilisateur?.nom?.toLocaleUpperCase()}`
                  : ""
            }
            readOnly
         />
      </>
   );
}

export default UtilisateurFormItemSelect;
