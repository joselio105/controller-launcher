<?php

namespace Plugse\Ctrl\http\routing;

use ArrayIterator;

class Routes extends ArrayIterator
{
    public function __construct(Route ...$routes)
    {
        parent::__construct($routes);
    }

    public function current(): Route
    {
        return parent::current();
    }

    public function offsetGet($offset): Route
    {
        return parent::offsetGet($offset);
    }
}
