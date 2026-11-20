<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Whatsnew\Kb;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

/**
 * What's New Plugin class for com_kb articles
 */
class Kb extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Return the alias and name for this category of content
     *
     * @return     array<mixed>
     */
    public function onWhatsnewAreas()
    {
        return [
            'kb' => Lang::txt('PLG_WHATSNEW_KB'),
        ];
    }

    /**
     * Pull a list of records that were created within the time frame ($period)
     *
     * @param      \Components\Whatsnew\Helpers\Period|mixed  $period     Time period to pull results for
     * @param      mixed   $limit      Number of records to pull
     * @param      integer $limitstart Start of records to pull
     * @param      array<mixed>|null   $areas      Active area(s)
     * @param      array<mixed>   $tagids     Array of tag IDs
     * @return     array<mixed>|int
     */
    public function onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])
    {
        if (is_array($areas) && $limit) {
            if (
                !isset($areas[$this->pluginName])
                && !in_array($this->pluginName, $areas)
            ) {
                return [];
            }
        }

        // Do we have a search term?
        if (!($period instanceof \Components\Whatsnew\Helpers\Period)) {
            return [];
        }

        $database = App::get('db');

        // Build the query
        $f_count = "SELECT COUNT(*)";
        $f_fields = "SELECT
			f.id,
			f.title,
			f.fulltxt AS `text`,
			concat('index.php?option=com_kb&section=', coalesce(concat(c.path, '/'), ''), f.alias) AS href,
			'kb' AS section,
			c.alias AS subsection";

        $f_from = " FROM `#__kb_articles` AS f
			LEFT JOIN `#__categories` AS c
				ON c.id = f.category
			WHERE f.state=1
				AND c.published = 1
				AND f.created > " . $database->quote($period->cStartDate) . "
				AND f.created < " . $database->quote($period->cEndDate) . "
				AND f.access IN ("
            . implode(',', User::getAuthorisedViewLevels()) . ")";

        $order_by = " ORDER BY f.created DESC, f.title";
        $order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

        if (!$limit) {
            // Get a count
            $database->setQuery($f_count . $f_from);
            return (int) $database->loadResult();
        } else {
            // Get results
            $database->setQuery($f_fields . $f_from . $order_by);
            $rows = $database->loadObjectList();

            foreach ($rows as $key => $row) {
                $rows[$key]->href = Route::url($row->href);
            }

            return $rows;
        }
    }
}
