<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models;

/**
 * Interface for OAI-PMH data provider
 */
interface Provider
{
    /**
     * Return data for sets
     *
     * @return string  The query that lists the sets
     */
    public function sets();

    /**
     * Return data for records
     *
     * @param  array<mixed>  $filters
     * @return string  The query that lists the records, or empty when the filters match none
     */
    public function records($filters = []);

    /**
     * Return a single record
     *
     * @param  integer  $id
     * @return object|null
     */
    public function record($id);
}
