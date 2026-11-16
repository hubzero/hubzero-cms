<?php

/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class MockProxy
{
    /**
     * @var  mixed
     */
    protected $class;

    /**
     * @param   array<mixed>  $args
     */
    public function __construct($args = [])
    {
        $this->class = $args['class'];
    }

    /**
     * @param   mixed  $name
     * @param   mixed  $args
     * @return  mixed
     */
    public function __call($name, $args)
    {
        $class = $this->class;

        $result = $class::$name(...$args);

        return $result;
    }
}
