/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React from "react";
import { Button, Card, Layout } from "antd";
import * as animationData from "./lottie-submitted.json";
import Lottie from "react-lottie";
import { useNavigate } from "react-router-dom";
import { env } from "../../env";

/**
 * @returns {React.ReactElement} The TypeDemande component with the TypeDemandeProvider and TypeDemandeContent.
 */
export default function TypeDemandeSoumise(): React.ReactElement {
   const navigate = useNavigate();
   const defaultIconOptions = {
      loop: false,
      autoplay: true,
      animationData: animationData,
      rendererSettings: {
         preserveAspectRatio: "xMidYMid slice",
      },
   };

   return (
      <Layout.Content className="d-flex-center" style={{ padding: "0 50px" }}>
         <Card style={{ minHeight: "20vh", minWidth: "60vw" }}>
            <div className="d-flex-center d-flex-column">
               <div className="text-center" style={{ height: "20vh" }}>
                  <Lottie options={defaultIconOptions} />
               </div>
               <div className="text-center">
                  <span className="semi-bold">Votre demande a été soumise.</span>
                  <br />
                  <p>
                     Elle sera très prochainement traitée par le service {env.REACT_APP_SERVICE}.
                     <br />
                     Vous pouvez suivre son avancement en vous rendant dans le menu
                     &laquo;&nbsp;Demandes&nbsp;&raquo;.
                  </p>
                  <p className="mt-2">
                     <Button type="primary" onClick={() => navigate("/demandes")}>
                        Retour à la liste des demandes
                     </Button>
                  </p>
               </div>
            </div>
         </Card>
      </Layout.Content>
   );
}
