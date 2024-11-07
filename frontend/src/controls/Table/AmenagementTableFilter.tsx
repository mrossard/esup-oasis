/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React from "react";
import { Avatar, Checkbox, Col, Collapse, Flex, Input, Row, Segmented, Select } from "antd";
import { CheckCircleFilled, FilterOutlined, HourglassOutlined } from "@ant-design/icons";
import { FiltresFavoris } from "./FiltresFavoris";
import { FiltreAmenagement, getFiltreAmenagementDefault } from "./AmenagementTableLayout";
import {
   AmenagementDomaine,
   DOMAINES_AMENAGEMENTS_INFOS,
   getDomaineAmenagement,
} from "../../lib/amenagements";
import {
   PREFETCH_CATEGORIES_AMENAGEMENTS,
   PREFETCH_COMPOSANTES,
   PREFETCH_FORMATIONS,
   PREFETCH_TAGS,
   PREFETCH_TYPES_AMENAGEMENTS,
   PREFETCH_TYPES_SUIVI_AMENAGEMENTS,
} from "../../api/ApiPrefetchHelpers";
import { useApi } from "../../context/api/ApiProvider";
import { ModeAffichageAmenagement } from "../../routes/gestionnaire/beneficiaires/Amenagements";
import { EtatAvisEse } from "../Avatars/BeneficiaireAvisEseAvatar";
import { FiltreFavoriDropDown } from "./FiltreFavoriDropDown";
import { useAuth } from "../../auth/AuthProvider";
import { Utilisateur } from "../../lib/Utilisateur";
import { NB_MAX_ITEMS_PER_PAGE } from "../../constants";
import { env } from "../../env";

export function AmenagementTableFilter(props: {
   filtreAmenagement: FiltreAmenagement;
   setFiltreAmenagement: React.Dispatch<React.SetStateAction<FiltreAmenagement>>;
   modeAffichage: ModeAffichageAmenagement;
}) {
   const user = useAuth().user;
   const { data: categoriesAmenagements } = useApi().useGetCollection({
      ...PREFETCH_CATEGORIES_AMENAGEMENTS,
      query: {
         "order[libelle]": "asc",
         // seulement ceux correspondant au domaine sélectionné
         "typesAmenagement.examens":
            props.filtreAmenagement.domaine === "examen" ? true : undefined,
         "typesAmenagement.aideHumaine":
            props.filtreAmenagement.domaine === "aideHumaine" ? true : undefined,
         "typesAmenagement.pedagogique":
            props.filtreAmenagement.domaine === "pedagogique" ? true : undefined,
      },
   });
   const { data: typesAmenagements } = useApi().useGetCollection({
      ...PREFETCH_TYPES_AMENAGEMENTS,
      query: {
         "order[libelle]": "asc",
         // seulement ceux correspondant au domaine sélectionné
         examens: props.filtreAmenagement.domaine === "examen" ? true : undefined,
         aideHumaine: props.filtreAmenagement.domaine === "aideHumaine" ? true : undefined,
         pedagogique: props.filtreAmenagement.domaine === "pedagogique" ? true : undefined,
      },
   });
   const { data: suivis } = useApi().useGetCollection(PREFETCH_TYPES_SUIVI_AMENAGEMENTS);
   const { data: composantes } = useApi().useGetCollection(PREFETCH_COMPOSANTES);
   const { data: formations } = useApi().useGetCollection(PREFETCH_FORMATIONS);
   const { data: tags } = useApi().useGetCollection(PREFETCH_TAGS);

   // Récupération de la liste des gestionnaires (hors renforts)
   const { data: gestionnaires, isFetching: isFetchingGestionnaires } =
      useApi().useGetCollectionPaginated({
         path: "/roles/{roleId}/utilisateurs",
         parameters: { roleId: "/roles/ROLE_GESTIONNAIRE" },
         page: 1,
         itemsPerPage: NB_MAX_ITEMS_PER_PAGE,
         query: { "order[nom]": "asc" },
         enabled: user?.isPlanificateur || user?.isRenfort,
      });

   const estRenfort = user && user.isRenfort && !user.isGestionnaire;
   const estReferent = user && user.isReferentComposante && !user.isGestionnaire;

   return (
      <Collapse
         accordion
         className="mb-3"
         items={[
            {
               key: "filter_save",
               label: (
                  <>
                     <FiltreFavoriDropDown
                        className="float-right mt-05"
                        setFiltre={props.setFiltreAmenagement}
                        filtreType={
                           props.modeAffichage === ModeAffichageAmenagement.ParAmenagement
                              ? "filtresAmenagement"
                              : "filtresAmenagementParBeneficiaire"
                        }
                     />
                     Filtres sauvegardés
                  </>
               ),
               children: (
                  <FiltresFavoris
                     filtre={props.filtreAmenagement}
                     setFiltre={props.setFiltreAmenagement}
                     filtreType={
                        props.modeAffichage === ModeAffichageAmenagement.ParAmenagement
                           ? "filtresAmenagement"
                           : "filtresAmenagementParBeneficiaire"
                     }
                     defaultFilter={getFiltreAmenagementDefault(user as Utilisateur)}
                  />
               ),
            },
            {
               key: "filters",
               label: (
                  <>
                     <FilterOutlined className="float-right" style={{ marginTop: 4 }} aria-hidden />
                     Filtres complémentaires
                  </>
               ),
               children: (
                  <>
                     <Row gutter={[16, 16]}>
                        <Col xs={24} sm={24} md={6}>
                           Nom du bénéficiaire
                        </Col>
                        <Col xs={24} sm={24} md={18}>
                           <Input
                              allowClear
                              placeholder="Nom du bénéficiaire"
                              value={props.filtreAmenagement.nom}
                              onChange={(e) => {
                                 props.setFiltreAmenagement((prev) => ({
                                    ...prev,
                                    nom: e.target.value,
                                    page: 1,
                                 }));
                              }}
                           />
                        </Col>

                        {!estRenfort && (
                           <>
                              <Col xs={24} sm={24} md={6}>
                                 Domaine
                              </Col>
                              <Col xs={24} sm={24} md={18}>
                                 <Flex justify="space-between" align="center">
                                    <Segmented
                                       disabled={estRenfort}
                                       options={[
                                          { label: "Tous", value: "Tous" },
                                          ...Object.keys(DOMAINES_AMENAGEMENTS_INFOS)
                                             .map((d) => DOMAINES_AMENAGEMENTS_INFOS[d])
                                             .sort((a, b) => a.order - b.order)
                                             .filter((d) => !estReferent || d.visibleReferent)
                                             .filter((d) => !estRenfort || d.visibleRenfort)
                                             .map((d) => ({
                                                label: (
                                                   <Flex align="center" gap={8}>
                                                      <Avatar
                                                         className={`bg-${d.couleur}`}
                                                         size={16}
                                                      />
                                                      <span>{d.singulier}</span>
                                                   </Flex>
                                                ),
                                                value: d.id,
                                             })),
                                       ]}
                                       style={{ overflowX: "auto", maxWidth: "100%" }}
                                       value={props.filtreAmenagement.domaine}
                                       onChange={(value) => {
                                          props.setFiltreAmenagement((prev) => {
                                             let restreindre =
                                                prev.restreindreColonnes && value !== "Tous";
                                             if (prev.domaine === "Tous" && value !== "Tous") {
                                                restreindre = true;
                                             }

                                             return {
                                                ...prev,
                                                domaine: value as AmenagementDomaine | "Tous",
                                                restreindreColonnes: restreindre,
                                                "type[]": [],
                                                "categorie[]": [],
                                                page: 1,
                                             };
                                          });
                                       }}
                                    />
                                    {props.modeAffichage ===
                                       ModeAffichageAmenagement.ParBeneficiaire &&
                                       props.filtreAmenagement.domaine !== "Tous" && (
                                          <Checkbox
                                             disabled={estRenfort}
                                             checked={props.filtreAmenagement.restreindreColonnes}
                                             onChange={(e) =>
                                                props.setFiltreAmenagement((prev) => ({
                                                   ...prev,
                                                   restreindreColonnes: e.target.checked,
                                                   page: 1,
                                                }))
                                             }
                                          >
                                             Restreindre les colonnes
                                          </Checkbox>
                                       )}
                                 </Flex>
                              </Col>
                           </>
                        )}

                        <>
                           <Col xs={24} sm={24} md={6}>
                              Catégories
                           </Col>
                           <Col xs={24} sm={24} md={18}>
                              <Select
                                 allowClear
                                 mode="tags"
                                 className="w-100"
                                 placeholder="Toutes les catégories"
                                 value={props.filtreAmenagement["categorie[]"]}
                                 onChange={(value) => {
                                    props.setFiltreAmenagement((prev) => ({
                                       ...prev,
                                       "categorie[]": value as string[],
                                       "type[]": [],
                                       page: 1,
                                    }));
                                 }}
                                 options={(categoriesAmenagements?.items || [])
                                    .filter((ca) => ca.actif)

                                    // Seulement ce qui est visble pour le profil de l'utilisateur
                                    .filter((ca) => {
                                       return typesAmenagements?.items.some((ta) => {
                                          const infos = getDomaineAmenagement(ta);
                                          return (
                                             ta.categorie === ca["@id"] &&
                                             ta.actif &&
                                             (user?.isGestionnaire ||
                                                (estRenfort && infos?.visibleRenfort) ||
                                                (estReferent && infos?.visibleReferent))
                                          );
                                       });
                                    })
                                    .sort((a, b) =>
                                       (a.libelle || "").localeCompare(b.libelle || ""),
                                    )
                                    .map((c) => ({
                                       label: c.libelle,
                                       value: c["@id"],
                                    }))}
                                 optionFilterProp="label"
                              />
                           </Col>
                        </>

                        <>
                           <Col xs={24} sm={24} md={6}>
                              Types
                           </Col>
                           <Col xs={24} sm={24} md={18}>
                              <Select
                                 allowClear
                                 mode="tags"
                                 className="w-100"
                                 placeholder="Tous les types"
                                 value={props.filtreAmenagement["type[]"]}
                                 onChange={(value) => {
                                    props.setFiltreAmenagement((prev) => ({
                                       ...prev,
                                       "type[]": value as string[],
                                       page: 1,
                                    }));
                                 }}
                                 options={(typesAmenagements?.items || [])
                                    .filter((t) => t.actif)

                                    // Seulement ce qui est visble pour le profil de l'utilisateur
                                    .filter((t) => {
                                       const infos = getDomaineAmenagement(t);
                                       return (
                                          (!estRenfort || infos?.visibleRenfort) &&
                                          (!estReferent || infos?.visibleReferent)
                                       );
                                    })

                                    //Le domaine sélectionné
                                    .filter(
                                       (t) =>
                                          props.filtreAmenagement.domaine === "Tous" ||
                                          getDomaineAmenagement(t)?.id ===
                                             props.filtreAmenagement.domaine,
                                    )

                                    .sort((a, b) =>
                                       (a.libelle || "").localeCompare(b.libelle || ""),
                                    )
                                    .map((c) => ({
                                       label: c.libelle,
                                       value: c["@id"],
                                    }))}
                                 optionFilterProp="label"
                              />
                           </Col>
                        </>

                        {(user?.isPlanificateur || user?.isRenfort) && (
                           <>
                              <Col xs={24} sm={24} md={6}>
                                 <span aria-label="Chargés d'accompagnement">
                                    Chargé•es d'accompagnement
                                 </span>
                              </Col>
                              <Col xs={24} sm={24} md={18}>
                                 <Select
                                    placeholder="Tous les chargés d'accompagnement"
                                    mode="tags"
                                    allowClear
                                    loading={isFetchingGestionnaires}
                                    className="w-100"
                                    options={gestionnaires?.items.map((g) => ({
                                       label: `${g.nom?.toLocaleUpperCase()} ${g.prenom}`,
                                       value: g["@id"],
                                    }))}
                                    value={props.filtreAmenagement["gestionnaire[]"]}
                                    onChange={(value) => {
                                       props.setFiltreAmenagement((prev) => ({
                                          ...prev,
                                          nomGestionnaire: undefined,
                                          "gestionnaire[]": value.length === 0 ? undefined : value,
                                          page: 1,
                                       }));
                                    }}
                                 />
                              </Col>
                           </>
                        )}

                        {!estRenfort && !estReferent && (
                           <>
                              <Col xs={24} sm={24} md={6}>
                                 Tags
                              </Col>
                              <Col xs={24} sm={24} md={18}>
                                 <Select
                                    allowClear
                                    mode="tags"
                                    className="w-100"
                                    placeholder="Tous les tags"
                                    value={props.filtreAmenagement["tags[]"]}
                                    onChange={(value) => {
                                       props.setFiltreAmenagement((prev) => ({
                                          ...prev,
                                          "tags[]": value as string[],
                                          page: 1,
                                       }));
                                    }}
                                    options={(tags?.items || [])
                                       .filter((ta) => ta.actif)
                                       .map((c) => ({
                                          label: c.libelle,
                                          value: c["@id"],
                                       }))}
                                    optionFilterProp="label"
                                 />
                              </Col>
                           </>
                        )}

                        {!estRenfort && !estReferent && (
                           <>
                              {props.modeAffichage === ModeAffichageAmenagement.ParBeneficiaire && (
                                 <>
                                    <Col xs={24} sm={24} md={6}>
                                       Avis {env.REACT_APP_ESPACE_SANTE_ABV || "santé"}
                                    </Col>
                                    <Col xs={24} sm={24} md={18}>
                                       <Segmented
                                          style={{ overflowX: "auto", maxWidth: "100%" }}
                                          onChange={(value) => {
                                             props.setFiltreAmenagement((prev) => ({
                                                ...prev,
                                                etatAvisEse:
                                                   value === "undefined"
                                                      ? undefined
                                                      : (value as EtatAvisEse),
                                                page: 1,
                                             }));
                                          }}
                                          options={[
                                             {
                                                label: "Tous",
                                                value: "undefined",
                                             },
                                             {
                                                label: "En attente",
                                                value: EtatAvisEse.ETAT_EN_ATTENTE,
                                                icon: (
                                                   <HourglassOutlined className="text-warning" />
                                                ),
                                             },
                                             {
                                                label: "En cours",
                                                value: EtatAvisEse.ETAT_EN_COURS,
                                                icon: (
                                                   <CheckCircleFilled className="text-success" />
                                                ),
                                             },
                                             {
                                                label: "Aucun",
                                                value: EtatAvisEse.ETAT_AUCUN,
                                             },
                                          ]}
                                       />
                                    </Col>
                                 </>
                              )}
                           </>
                        )}

                        {props.modeAffichage === ModeAffichageAmenagement.ParAmenagement && (
                           <Col xs={24} sm={24} md={6}>
                              Suivis
                           </Col>
                        )}
                        {props.modeAffichage === ModeAffichageAmenagement.ParAmenagement && (
                           <Col xs={24} sm={24} md={18}>
                              <Select
                                 allowClear
                                 mode="tags"
                                 className="w-100"
                                 placeholder="Tous les suivis"
                                 value={props.filtreAmenagement["suivis[]"]}
                                 onChange={(value) => {
                                    props.setFiltreAmenagement((prev) => ({
                                       ...prev,
                                       "suivis[]": value as string[],
                                       page: 1,
                                    }));
                                 }}
                                 options={(suivis?.items || [])
                                    .filter((s) => s.actif)
                                    .map((c) => ({
                                       label: c.libelle,
                                       value: c["@id"],
                                    }))}
                                 optionFilterProp="label"
                              />
                           </Col>
                        )}

                        <Col xs={24} sm={24} md={6}>
                           Composantes
                        </Col>
                        <Col xs={24} sm={24} md={18}>
                           <Select
                              allowClear
                              mode="tags"
                              className="w-100"
                              placeholder="Toutes les composantes"
                              value={props.filtreAmenagement["composante[]"]}
                              onChange={(value) => {
                                 props.setFiltreAmenagement((prev) => ({
                                    ...prev,
                                    "composante[]": value as string[],
                                    page: 1,
                                 }));
                              }}
                              options={(composantes?.items || []).map((c) => ({
                                 label: c.libelle,
                                 value: c["@id"],
                              }))}
                              optionFilterProp="label"
                           />
                        </Col>

                        <Col xs={24} sm={24} md={6}>
                           Formations
                        </Col>
                        <Col xs={24} sm={24} md={18}>
                           <Select
                              allowClear
                              mode="tags"
                              className="w-100"
                              placeholder="Toutes les formations"
                              value={props.filtreAmenagement["formation[]"]}
                              onChange={(value) => {
                                 props.setFiltreAmenagement((prev) => ({
                                    ...prev,
                                    "formation[]": value as string[],
                                    page: 1,
                                 }));
                              }}
                              options={(formations?.items || []).map((c) => ({
                                 label: `[${c.codeExterne?.replace("#", "-")}] ${c.libelle}`,
                                 value: c["@id"],
                              }))}
                              optionFilterProp="label"
                           />
                           <div className="legende">
                              Seules les formations ayant au moins un bénéficiaire sont proposées.
                           </div>
                        </Col>
                     </Row>
                  </>
               ),
            },
         ]}
      />
   );
}
