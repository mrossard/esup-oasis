<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\State\Pagination\HasNextPagePaginatorInterface;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ArrayObject;
use Iterator;
use IteratorAggregate;
use Traversable;

class MappedCollectionPaginator implements IteratorAggregate, PaginatorInterface, HasNextPagePaginatorInterface
{
    public function __construct(
        private PaginatorInterface $decorated,
        private $mapping,
    ) {}

    public function getIterator(): Traversable
    {
        foreach ($this->decorated as $item) {
            yield ($this->mapping)($item);
        }
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
