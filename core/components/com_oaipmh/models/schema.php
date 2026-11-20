<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models;

/**
 * Interface for OAI-PMH Schemas
 */
interface Schema
{
    /**
     * Check if a schema format is handled
     *
     * @param  string  $type
     * @return bool
     */
    public static function handles($type);

    /**
     * Set the service the schema writes for
     *
     * @param   Service  $service
     * @return  object
     */
    public function setService(Service &$service);

    /**
     * Set the response the schema writes to
     *
     * @param   Xml\Response  $response
     * @return  object
     */
    public function setResponse(Xml\Response &$response);

    /**
     * Output set data
     *
     * @param  array<mixed>  $set
     * @return $this
     */
    public function set($set);

    /**
     * Output a lsit of records
     *
     * @param  array<mixed>    $iterator
     * @param  boolean  $metadata
     * @return $this
     */
    public function records($iterator, $metadata = true);

    /**
     * Output a record
     *
     * @param  object   $result
     * @param  boolean  $metadata
     * @return $this
     */
    public function record($result, $metadata = true);
}
