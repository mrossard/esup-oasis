/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { Card, Col, Layout, Row } from "antd";
import PageTitle from "../../utils/PageTitle/PageTitle";
import { env } from "../../env";

/**
 * Page des mentions légales
 */
export default function MentionsLegales() {
   return (
      <Layout style={{ minHeight: "100vh" }}>
         <PageTitle />
         <Layout.Content style={{ padding: 50 }}>
            <Card title={<h1>Mentions légales</h1>} className="mb-3">
               {/* ------ INSEREZ ICI VOTRE TEXTE DE MENTIONS LEGALES ------ */}
            </Card>
            <Card title={<h1>Crédits</h1>} className="mb-3">
               <Row>
                  {env.REACT_APP_LOGO && (
                     <Col md={8}>
                        <a
                           href={env.REACT_APP_ETABLISSEMENT_URL as string}
                           target="_blank"
                           rel="noreferrer"
                           referrerPolicy="no-referrer"
                        >
                           <img src={env.REACT_APP_LOGO} alt="Logo" style={{ height: 100 }} />
                        </a>
                     </Col>
                  )}
                  <Col md={8}>
                     <a
                        href="https://www.esup-portail.org/"
                        target="_blank"
                        rel="noreferrer"
                        referrerPolicy="no-referrer"
                     >
                        <img
                           src="/images/logo_esup.svg"
                           alt="Logo Esup Portail"
                           style={{ height: 100 }}
                        />
                     </a>
                  </Col>
                  <Col md={8}>
                     <a
                        href="https://www.u-bordeaux.fr/"
                        target="_blank"
                        rel="noreferrer"
                        referrerPolicy="no-referrer"
                     >
                        <img
                           src="/images/logo_ub.svg"
                           alt="Logo Université de Bordeaux"
                           style={{ height: 100 }}
                        />
                     </a>
                  </Col>
               </Row>
               <h2>Développement</h2>
               <p>
                  Application développée par la Direction des Systèmes d'Information (DSI) de l'
                  <a
                     href="https://www.u-bordeaux.fr/"
                     target="_blank"
                     rel="noreferrer"
                     referrerPolicy="no-referrer"
                  >
                     université de Bordeaux
                  </a>{" "}
                  pour le Service{" "}
                  <a
                     href="https://www.u-bordeaux.fr/formation/accompagnement-et-reussite-des-etudes/etudiants-besoins-specifiques"
                     target="_blank"
                     rel="noreferrer"
                     referrerPolicy="no-referrer"
                  >
                     PHASE
                  </a>
                  .
               </p>
               <h2>Mise à disposition</h2>
               <p>
                  Application mise à disposition des établissements d'enseignement supérieur via le
                  consortium ESUP (
                  <a
                     target="_blank"
                     referrerPolicy="no-referrer"
                     href="https://www.esup-portail.org/"
                     rel="noreferrer"
                  >
                     https://www.esup-portail.org/
                  </a>
                  ) - Communauté d'établissements français d'enseignement supérieur pour
                  l'innovation numérique.
               </p>
               <h2>Licence</h2>
               <p>Licence Apache version 2 (Janvier 2004)</p>
               <h2>Crédits images</h2>
               <p>
                  Images mises à disposition par{" "}
                  <a
                     href="https://www.freepik.com/"
                     target="_blank"
                     rel="noreferrer"
                     referrerPolicy="no-referrer"
                  >
                     freepik.com
                  </a>{" "}
                  - Designed by Freepik, adaptées par l'université de Bordeaux.
               </p>
            </Card>
            <div className="text-center">
               <a href="/">Retour à l'accueil</a>
            </div>
         </Layout.Content>
      </Layout>
   );
}
