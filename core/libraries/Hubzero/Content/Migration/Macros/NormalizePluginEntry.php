<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Give a plugin's extension entry the name it should have
 *
 * Migrations have called for this since 2014 and nothing ever answered, so
 * every one of them stopped the run. It does the plain reading of its name:
 * a plugin's entry is found by its folder and element, and its name is set to
 * the one every entry written since has: plg_<folder>_<element>. Nothing is
 * created, because a plugin that has no entry is not one this can normalize.
 **/
class NormalizePluginEntry extends Macro
{
    /**
     * Normalize an entry
     *
     * @param   string  $folder   Plugin folder
     * @param   string  $element  Plugin element
     * @return  bool
     */
    public function __invoke($folder, $element)
    {
        if (!$this->db->tableExists('#__extensions')) {
            $this->log(
                sprintf('Required table not found for plugin "plg_%s_%s"', $folder, $element),
                'warning'
            );

            return false;
        }

        $folder  = strtolower($folder);
        $element = strtolower($element);
        $name    = 'plg_' . $folder . '_' . $element;

        $entry = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->whereEquals('type', 'plugin')
            ->whereEquals('folder', $folder)
            ->whereEquals('element', $element)
            ->value('extension_id');

        if (!$entry) {
            $this->log(sprintf('No entry to normalize for plugin "%s"', $name));
            return false;
        }

        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set(['name' => $name])
            ->whereEquals('extension_id', $entry)
            ->execute();

        $this->log(sprintf('Normalized extension entry for plugin "%s"', $name));

        return true;
    }
}
