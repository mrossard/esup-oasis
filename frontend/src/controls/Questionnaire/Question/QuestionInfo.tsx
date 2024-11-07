/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import { Form } from "antd";
import { QuestionnaireQuestion } from "../../../context/demande/QuestionnaireProvider";

export function QuestionInfo(props: { question: QuestionnaireQuestion }) {
   // props.question.libelle contient du HTML
   return (
      <>
         <Form.Item className="mb-0" rootClassName="question-item" name={props.question["@id"]}>
            <span dangerouslySetInnerHTML={{ __html: props.question.aide as string }} />
         </Form.Item>
      </>
   );
}
