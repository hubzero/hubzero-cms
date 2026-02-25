<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Whatsnew\Publications;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Date;

/**
 * What's New Plugin class for com_publications entries
 */
class Publications extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;
    // @phpcs:enable PSR2.Classes.PropertyDeclaration.Underscore

    /**
     * Resource types and "all" category
     *
     * @var  array
     */
    private $areas = null;

    /**
     * Resource types
     *
     * @var  array
     */
    private $cats = null;

    /**
     * Results total
     *
     * @var  integer
     */
    private $total = null;

    /**
     * Constructor
     *
     * @param   object  &$subject  Event observer
     * @param   array   $config    Optional config values
     * @return  void
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * Return the alias and name for this category of content
     *
     * @return  array
     */
    public function onWhatsnewAreas()
    {
        return array(
            'publications' => Lang::txt('PLG_WHATSNEW_PUBLICATIONS')
        );
    }

    /**
     * Pull a list of records that were created within the time frame ($period)
     *
     * @param   object   $period      Time period to pull results for
     * @param   mixed    $limit       Number of records to pull
     * @param   integer  $limitstart  Start of records to pull
     * @param   array    $areas       Active area(s)
     * @param   array    $tagids      Array of tag IDs
     * @return  array
     */
    public function onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = array())
    {
        if (is_array($areas) && $limit) {
            if (
                !isset($areas[$this->_name])
                && !in_array($this->_name, $areas)
            ) {
                return array();
            }
        }

        // Do we have a time period?
        if (!is_object($period)) {
            return array();
        }

        $database = App::get('db');

        // Instantiate some needed objects
        $rr = new \Components\Publications\Tables\Publication($database);

        // Build query
        $filters = array(
            'startdate' => $period->cStartDate,
            'enddate' => $period->cEndDate,
            'sortby' => 'date'
        );
        if (count($tagids) > 0) {
            $filters['tag'] = $tagids;
        }

        if ($limit) {
            if ($this->total != null) {
                $total = 0;
                $t = $this->total;
                foreach ($t as $l) {
                    $total += $l;
                }
                if ($total == 0) {
                    return array();
                }
            }

            $filters['limit'] = $limit;
            $filters['start'] = $limitstart;

            // Get results
            $rows = $rr->getRecords($filters);

            // Did we get any results?
            if ($rows) {
                // Loop through the results and set each item's HREF
                foreach ($rows as $key => $row) {
                    $rows[$key]->text = null;
                    if ($row->alias) {
                        $rows[$key]->href = Route::url(
                            'index.php?option=com_publications&alias=' . $row->alias
                        );
                    } else {
                        $rows[$key]->href = Route::url(
                            'index.php?option=com_publications&id=' . $row->id
                        );
                    }
                    if ($row->abstract) {
                        $rows[$key]->text = $rows[$key]->abstract;
                    }
                    $rows[$key]->section = null;
                    $rows[$key]->area = $row->cat_name;
                    $rows[$key]->publish_up = $row->published_up;
                }
            }

            return $rows;
        } else {
            // Get a count
            $counts = array();

            // Execute count query
            $results = $rr->getCount($filters);

            return ($results && is_array($results)) ? count($results) : $results;
        }
    }

    /**
     * Push styles and scripts to the document
     *
     * @return  void
     */
    public static function documents()
    {
        \Hubzero\Document\Assets::addComponentStylesheet('com_publications');
    }

    /**
     * Special formatting for results
     *
     * @param   object  $row     Database row
     * @param   string  $period  Time period
     * @return  string
     */
    public static function out($row, $period)
    {
        $database = App::get('db');

        $config = Component::params('com_publications');

        // Get version authors
        $pa = new \Components\Publications\Tables\Author($database);
        $authors = $pa->getAuthors($row->version_id);

        // Start building HTML
        $html = "\t" . '<li class="publication">' . "\n";
        $imgUrl = Route::url(
            'index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id
        ) . '/Image:thumb';
        $html .= "\t\t" . '<p><span class="pub-thumb"><img src="' . $imgUrl . '" alt="" /></span>';
        $html .= '<span class="pub-details"><a href="' . $row->href . '">'
            . stripslashes($row->title) . '</a>' . "\n";
        $html .= "\t\t" . '<span class="block details">'
            . Date::of($row->published_up)->toLocal('d M Y') . ' <span>|</span> ' . $row->cat_name;
        if ($authors) {
            $html .= ' <span>|</span> ' . Lang::txt('PLG_WHATSNEW_PUBLICATIONS_CONTRIBUTORS') . ' '
                . \Components\Publications\Helpers\Html::showContributors($authors, false, true);
        }
        $html .= '</span></span></p>' . "\n";
        if ($row->text) {
            $text = \Hubzero\Utility\Str::truncate(
                \Hubzero\Utility\Sanitize::stripAll(stripslashes($row->text)),
                200
            );
            $html .= "\t\t" . '<p>' . $text . '</p>' . "\n";
        }
        $html .= "\t\t" . '<p class="href">' . Request::base() . trim($row->href, '/') . '</p>' . "\n";
        $html .= "\t" . '</li>' . "\n";

        // Return output
        return $html;
    }
}
