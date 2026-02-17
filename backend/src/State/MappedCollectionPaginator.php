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
        return new class($this->mapping, $this->decorated->getIterator()) implements Iterator {
            private array $mappingCache = [];

            public function __construct(
                private $mapping,
                private readonly Iterator $decorated,
            ) {}

            public function current(): mixed
            {
                $current = $this->decorated->current();
                $key = spl_object_hash($current);

                $this->mappingCache[$key] ??= ($this->mapping)($current);

                return $this->mappingCache[$key];
            }

            public function next(): void
            {
                $this->decorated->next();
            }

            public function key(): mixed
            {
                return $this->decorated->key();
            }

            public function valid(): bool
            {
                return $this->decorated->valid();
            }

            public function rewind(): void
            {
                $this->decorated->rewind();
            }
        };
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
