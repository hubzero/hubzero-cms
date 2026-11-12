<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import\Interfaces;

/**
 * Interface for Resource Importer
 */
interface Adapter
{
    /**
     * Does this adapter respond to a mime type
     *
     * @param   string  $mime  Mime type string
     * @return  bool
     */
    public static function accepts($mime);

    /**
     * Count Import data
     *
     * @param   \Components\Resources\Models\Import $import
     * @return  int
     */
    public function count(\Components\Resources\Models\Import $import);

    /**
     * Process Import data
     *
     * @param   \Components\Resources\Models\Import $import
     * @param   array    $callbacks
     * @param   integer  $dryRun
     * @return  array
     */
    public function process(\Components\Resources\Models\Import $import, array $callbacks, $dryRun);
}
