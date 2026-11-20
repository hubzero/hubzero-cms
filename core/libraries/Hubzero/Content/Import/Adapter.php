<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import;

use Hubzero\Content\Import\Model\Import;

/**
 * Interface for Import Adapters
 */
interface Adapter
{
    /**
     * Check if a mime type is accepted
     *
     * @param  string  $mime
     * @return bool
     */
    public static function accepts($mime);

    /**
     * Count import records
     *
     * @param  \Hubzero\Content\Import\Model\Import $import
     * @return int
     */
    public function count(Import $import);

    /**
     * The headers (field names) of the import's records
     *
     * @param  \Hubzero\Content\Import\Model\Import $import
     * @return array<mixed>
     */
    public function headers(Import $import);

    /**
     * Process an import
     *
     * @param  \Hubzero\Content\Import\Model\Import $import
     * @param  array<mixed>    $callbacks
     * @param  bool     $dryRun
     * @return array<mixed>
     */
    public function process(Import $import, array $callbacks, $dryRun);
}
