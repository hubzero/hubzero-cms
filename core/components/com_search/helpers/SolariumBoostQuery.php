<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class SolariumBoostQuery
{
    protected $boost;
    protected $query;

    /**
     * @param   mixed  $args
     */
    public function __construct($args = [])
    {
        $this->query = null;
        $this->boost = $args['boost'];
    }

    /**
     * @return  array<string, mixed>
     */
    public function toArray()
    {
        $query = $this->getQuery();

        return ['query' => $query];
    }

    /**
     * @return  mixed
     */
    protected function getQuery()
    {
        if (!$this->query) {
            $this->query = $this->generateQuery();
        }

        return $this->query;
    }

    /**
     * @return  string
     */
    protected function generateQuery()
    {
        $field = $this->boost->getField();
        $fieldValue = $this->boost->getFieldValue();
        $strength = $this->boost->getStrength();

        return "$field:$fieldValue^$strength";
    }

    /**
     * @param   array<string, mixed>  $args
     * @return  \Components\Search\Helpers\SolariumBoostQuery
     */
    public static function one($args)
    {
        return new self($args);
    }
}
