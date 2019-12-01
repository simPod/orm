<?php

declare(strict_types=1);

namespace Doctrine\ORM\Internal\Hydration;

use Iterator;
use function array_values;
use function is_array;

/**
 * Represents a result structure that can be iterated over, hydrating row-by-row
 * during the iteration. An IterableResult is obtained by AbstractHydrator#getIterable().
 */
final class RowByRowResult implements Iterator
{
    /** @var AbstractHydrator */
    private $hydrator;

    /** @var bool */
    private $rewinded = false;

    /** @var int */
    private $key = -1;

    /** @var object|null */
    private $current;

    public function __construct(AbstractHydrator $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @throws HydrationException
     */
    public function rewind() : void
    {
        if ($this->rewinded === true) {
            throw new HydrationException('Can only iterate a Result once.');
        }

        $this->current  = $this->next();
        $this->rewinded = true;
    }

    /**
     * Gets the next set of results.
     *
     * @return mixed|false
     */
    public function next()
    {
        $this->current = $this->hydrator->hydrateRow();
        if (is_array($this->current)) {
            $this->current = array_values($this->current)[0];
        }

        $this->key++;

        return $this->current;
    }

    /** @return mixed */
    public function current()
    {
        return $this->current;
    }

    public function key() : int
    {
        return $this->key;
    }

    public function valid() : bool
    {
        return $this->current !== false;
    }
}
