<?php

/*
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

        return $class::$name(...$args);
    }
}
