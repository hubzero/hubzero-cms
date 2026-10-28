<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

use Hubzero\Base\Obj;

include_once __DIR__ . DS . 'orm' . DS . 'tool.php';

/**
 * Tools Model
 */
class Tools extends Obj
{
    /**
     * The tools that have a forge directory, with whether each is live
     *
     * A tool is live when its pipeline state is published, or when it is
     * stalled at installed, updated or approved but has a published version
     * and a published, non-private tool resource: the pipeline state of
     * long-lived tools drifts, and is not a reliable record on its own.
     * Retired and abandoned tools, and tools in earlier states, are never
     * returned.
     *
     * Each row carries the tool's newest published version (else its newest
     * development version) for title and source access, picked here so the
     * values are deterministic.
     *
     * @return  array  Objects with id, instance, toolname, title, toolaccess,
     *                 codeaccess, revision, state, tool_state and live
     */
    public function getApplicationTools()
    {
        $dh = @opendir('/opt/trac/tools');
        $dirs = array();

        if (empty($dh)) {
            return $dirs;
        }

        while (($file = readdir($dh)) !== false) {
            if (strncmp($file, '.', 1) != 0 && is_dir('/opt/trac/tools/' . $file)) {
                $dirs[] = $file;
            }
        }
        closedir($dh);

        return $this->forgeTools($dirs);
    }

    /**
     * The listable tools among the given forge directory names
     *
     * @param   array  $dirs  Tool names that have a forge directory
     * @return  array  See getApplicationTools()
     */
    public function forgeTools(array $dirs)
    {
        if (!count($dirs)) {
            return array();
        }

        $database = \App::get('db');

        $names = array_map(array($database, 'quote'), $dirs);

        $stalled   = array(Orm\Tool::STATUS_INSTALLED, Orm\Tool::STATUS_UPDATED, Orm\Tool::STATUS_APPROVED);
        $listable  = array_merge($stalled, array(Orm\Tool::STATUS_PUBLISHED));

        // One row per version; whether a published, non-private standalone
        // tool resource exists for the tool's alias rides along
        $database->setQuery(
            "SELECT v.id, v.instance, v.toolname, v.title, v.revision, v.toolaccess, v.codeaccess, v.state,
				t.state AS tool_state,
				(SELECT COUNT(*) FROM `#__resources` AS r
					WHERE r.alias=v.toolname AND r.type=7 AND r.standalone=1
					AND r.published=1 AND r.access!=4) AS resource_live
			FROM `#__tool` AS t
			INNER JOIN `#__tool_version` AS v ON v.toolid=t.id
			WHERE v.toolname IN (" . implode(',', $names) . ")
			AND v.state IN (1, 3)
			AND t.state IN (" . implode(',', $listable) . ")
			ORDER BY v.toolname ASC, (v.state=1) DESC, v.revision DESC, v.id DESC"
        );

        $tools = array();
        foreach ((array) $database->loadObjectList() as $row) {
            // The first row per tool is its newest published version,
            // or failing that its newest development version
            if (isset($tools[$row->toolname])) {
                continue;
            }

            $published = ($row->state == 1);
            $row->live = ($row->tool_state == Orm\Tool::STATUS_PUBLISHED)
                || (in_array((int) $row->tool_state, $stalled) && $published && $row->resource_live > 0);

            unset($row->resource_live);
            $tools[$row->toolname] = $row;
        }

        return array_values($tools);
    }
}
