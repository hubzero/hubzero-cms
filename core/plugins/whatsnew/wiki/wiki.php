<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Whatsnew\Wiki;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

/**
 * What's New Plugin class for com_wiki articles
 */
class Wiki extends Plugin
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
     * @return  array
     */
    public function onWhatsnewAreas()
    {
        return [
            'wiki' => Lang::txt('PLG_WHATSNEW_WIKI'),
        ];
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

        // Do we have a time period?
        if (!is_object($period)) {
            return [];
        }

        // @TODO: Move these to separate plugins so Wiki doesn't directly reference other extensions

        if (!$limit) {
            return \Components\Wiki\Models\Page::all()
                ->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
                ->where('created', '>=', $period->cStartDate)
                ->where('created', '<', $period->cEndDate)
                ->order('created', 'desc')
                ->count();
        } else {
            $pages = \Components\Wiki\Models\Page::all()
                ->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
                ->order('created', 'desc')
                ->where('created', '>=', $period->cStartDate)
                ->where('created', '<', $period->cEndDate)
                ->limit($limit)
                ->start($limitstart)
                ->rows();

            $rows = [];

            foreach ($pages as $page) {
                $row = new \stdClass();
                $row->title = $page->title;
                $row->href = Route::url($page->link());
                $row->text = strip_tags($page->version->get('pagehtml', ''));
                $row->category = $page->get('scope');
                $row->section = $this->pluginName;

                $rows[] = $row;
            }

            return $rows;
        }
    }
}
