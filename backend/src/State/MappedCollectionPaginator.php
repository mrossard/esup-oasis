<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\State\Pagination\HasNextPagePaginatorInterface;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ArrayObject;
use IteratorAggregate;
use Traversable;

class MappedCollectionPaginator implements IteratorAggregate, PaginatorInterface, HasNextPagePaginatorInterface
{
    private TraversablePaginator $decorated;

    public function __construct(PaginatorInterface $entityPaginator, callable $mapping)
    {
        $this->decorated = new TraversablePaginator(
            new ArrayObject(array_map($mapping, iterator_to_array($entityPaginator))),
            $entityPaginator->getCurrentPage(),
            $entityPaginator->getItemsPerPage(),
            $entityPaginator->getTotalItems(),
        );
    }

    public function getIterator(): Traversable
    {
        return $this->decorated->getIterator();
    }

    public function count(): int
    {
        return $this->decorated->count();
    }

    public function hasNextPage(): bool
    {
        return $this->decorated->hasNextPage();
    }

    public function getLastPage(): float
    {
        return $this->decorated->getLastPage();
    }

    public function getTotalItems(): float
    {
        return $this->decorated->getTotalItems();
    }

    public function getCurrentPage(): float
    {
        return $this->decorated->getCurrentPage();
    }

    public function getItemsPerPage(): float
    {
        return $this->decorated->getItemsPerPage();
    }
}
