<?php

/*
 * Copyright (c) 2024-2026. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

declare(strict_types=1);

namespace App\Filter\Util;

use ApiPlatform\Metadata\IriConverterInterface;

final readonly class IriToIdResolver
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {}

    public function resolve(mixed $val): ?int
    {
        if ($val === null || $val === '') {
            return null;
        }

        if (is_numeric($val)) {
            return (int) $val;
        }

        if (is_string($val) && str_starts_with($val, '/')) {
            return $this->resolveFromIri($val);
        }

        return null;
    }

    private function resolveFromIri(string $val): ?int
    {
        try {
            $item = $this->iriConverter->getResourceFromIri($val, ['fetch_data' => false]);
            if (($id = $item->id ?? null) !== null) {
                return (int) $id;
            }
            if (method_exists($item, 'getId') && ($id = $item->getId()) !== null) {
                return (int) $id;
            }
        } catch (\Throwable) {
            $item = null;
        }

        if (preg_match('#/(\d+)$#', $val, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
