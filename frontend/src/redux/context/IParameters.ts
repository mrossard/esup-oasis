/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

export interface IParameterValue {
   name: string;
   value: string;
}

export interface IParameters {
   parameters: IParameterValue[];
}

export const initialParameters: IParameters = {
   parameters: [],
};