<?php

/**
 * Interface for Dataviewer mode classes.
 *
 * Mode classes resolve database connections and data definitions
 * from different sources (filesystem, datastores, project datasets).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Helpers;

interface ModeInterface
{
    /**
     * Get database connection config for this mode.
     *
     * Returns an array with at minimum: host, user, password, database.
     *
     * @param   array  $dbId    Database identifier array
     * @param   array  $config  Base config array (modified by reference)
     * @return  array  Updated config array
     */
    public function getConfig(array $dbId, array &$config): array;

    /**
     * Get the data definition for the requested dataview.
     *
     * @param   array  $dbId    Database identifier array
     * @param   array  $config  Config array (may be modified)
     * @return  array|null  Data definition array, or null if not found
     */
    public function getDataDefinition(array $dbId, array &$config): ?array;

    /**
     * Set breadcrumb pathway for the dataview.
     *
     * @param   array  $dd  Data definition array
     * @return  void
     */
    public function setPathway(array $dd): void;
}
