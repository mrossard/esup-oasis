<?php

namespace App\State;

use ApiPlatform\Metadata\IriConverterInterface;

readonly class EntityToResourceTransformer
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {}

    public static function entityToResource($emptyResourceObject, $entity, $iriConverter = null)
    {
        $resourceClass = get_class($emptyResourceObject);
        return new $resourceClass($entity, $iriConverter);
    }

    //    function __invoke($emptyResourceObject, $entity)
    //    {
    //        dd('hello');
    //        return $this->entityToResource($emptyResourceObject, $entity, $this->iriConverter);
    //    }
    //    public function getIriConverter(): IriConverterInterface
    //    {
    //        return $this->iriConverter;
    //    }
}
