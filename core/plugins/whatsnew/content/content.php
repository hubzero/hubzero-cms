<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Whatsnew\Content;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

/**
 * What's New Plugin class for com_content articles
 */
class Content extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Return the alias and name for this category of content
     *
     * @return  array<mixed>
     */
    public function onWhatsnewAreas()
    {
        return [
            'content' => Lang::txt('PLG_WHATSNEW_CONTENT'),
        ];
    }

    /**
     * Pull a list of records that were created within the time frame ($period)
     *
     * @param   \Components\Whatsnew\Helpers\Period|mixed   $period      Time period to pull results for
     * @param   mixed    $limit       Number of records to pull
     * @param   integer  $limitstart  Start of records to pull
     * @param   array<mixed>|null    $areas       Active area(s)
     * @param   array<mixed>    $tagids      Array of tag IDs
     * @return  array<mixed>|int
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
        $c_count = " SELECT count(DISTINCT c.id)";
        $c_fields = " SELECT c.id, c.title, c.alias, c.created, b.path, "
            . "CONCAT(c.introtext, c.fulltext) AS text, "
            . "CONCAT('index.php?option=com_content&task=view&id=', c.id) AS href, "
            . "NULL AS fsection, b.alias AS category, 'content' AS section, NULL AS subsection";
        $c_from = " FROM #__content AS c INNER JOIN #__categories AS b ON b.id=c.catid";

        $c_where = "c.publish_up > " . $database->quote($period->cStartDate)
            . " AND c.publish_up < " . $database->quote($period->cEndDate) . " AND c.state='1'";

        $order_by = " ORDER BY publish_up DESC, title";
        $order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

        $matches = null;
        $tag = null;
        if ($limit) {
            // Get results
            $database->setQuery($c_fields . $c_from . " WHERE " . $c_where . $order_by);
            $rows = $database->loadObjectList();

            if ($rows) {
                foreach ($rows as $key => $row) {
                    $path = Route::url($row->href);
                    if ($row->path) {
                        $path = ltrim(Request::base(true), '/') . '/' . $row->path . '/' . $row->alias;
                    }

                    preg_match_all("/\{xhub:\s*[^\}]*\}/i", $rows[$key]->text, $matches, PREG_SET_ORDER);
                    if ($matches) {
                        foreach ($matches as $match) {
                            if (preg_match("/\{xhub:\s*([^\s]+)\s*(.*)/i", $match[0], $tag)) {
                                switch (strtolower(trim($tag[1]))) {
                                    case 'include':
                                        $rows[$key]->text = str_replace($match[0], '', $rows[$key]->text);
                                        break;
                                }
                            }
                        }
                    }

                    $rows[$key]->text = Html::content('prepare', $rows[$key]->text, '', 'com_content.article');
                    $rows[$key]->text = strip_tags($row->text);
                    $rows[$key]->href = $path;
                }
            }

            return $rows;
        } else {
            // Get a count
            $database->setQuery($c_count . $c_from . " WHERE " . $c_where);
            return (int) $database->loadResult();
        }
    }

    /**
     * Special formatting for results
     *
     * @param   \stdClass  $row     Database row
     * @param   string  $period  Time period
     * @return  string
     */
    public static function out($row, $period)
    {
        if (strstr($row->href, 'index.php')) {
            $row->href = Route::url($row->href);
        }

        $html = "\t" . '<li>' . "\n";
        $html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">'
            . htmlspecialchars(stripslashes($row->title), ENT_COMPAT, 'UTF-8') . '</a></p>' . "\n";
        if ($row->text) {
            $text = \Hubzero\Utility\Str::truncate(
                \Hubzero\Utility\Sanitize::stripAll(stripslashes($row->text)),
                200,
            );
            $html .= "\t\t" . '<p>' . $text . '</p>' . "\n";
        }
        $html .= "\t\t" . '<p class="href">' . Request::base() . ltrim($row->href, '/') . '</p>' . "\n";
        $html .= "\t" . '</li>' . "\n";

        // Return output
        return $html;
    }
}
