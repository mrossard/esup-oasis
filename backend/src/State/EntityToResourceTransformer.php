<?php

namespace App\State;

class EntityToResourceTransformer
{
    public static function entityToResource($emptyResourceObject, $entity)
    {
        $resourceClass = get_class($emptyResourceObject);
        return new $resourceClass($entity);
    }
}
