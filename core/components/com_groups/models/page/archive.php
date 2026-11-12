<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Obj;
use Hubzero\Base\Model\ItemList;
use Hubzero\Facades\App;
use Hubzero\Facades\Request;

/**
 * Group page archive model class
 *
 */
class Archive extends Obj
{
    /**
     * \Hubzero\Base\Model
     *
     * @var object
     */
    private $pages = null;

    /**
     * Page count
     *
     * @var integer
     */
    private $pagesCount = null;

    /**
     * Database
     *
     * @var object
     */
    private $db = null;

    /**
     * \Hubzero\User\Group
     *
     * @var object
     */
    private $group = null;

    /**
     * Constructor
     *
     * @return     void
     */
    public function __construct()
    {
        $this->db    = App::get('db');
        $this->group = \Hubzero\User\Group::getInstance(Request::getString('cn', ''));
    }

    /**
     * Get Instance of Page Archive
     *
     * @param   string $key Instance Key
     * @return  object \Components\Groups\Models\PageArchive
     */
    public static function &getInstance($key = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self();
        }

        return $instances[$key];
    }

    /**
     * Get a list of group pages
     *
     * @param      string  $rtrn    What data to return
     * @param      array   $filters Filters to apply to data retrieval
     * @param      boolean $clear Clear cached data?
     * @return     mixed
     */
    public function pages($rtrn = 'list', $filters = [], $clear = false)
    {
        switch (strtolower($rtrn)) {
            case 'alias':
                $aliases = [];
                if ($results = $this->pages('list', $filters, true)) {
                    foreach ($results as $result) {
                        $aliases[] = $result->get('alias');
                    }
                }
                return $aliases;
            case 'unapproved':
                $unapproved = [];
                if ($results = $this->pages('list', $filters, true)) {
                    foreach ($results as $k => $result) {
                        // get current version
                        $version = $result->versions()->first();

                        if (!$version) {
                            continue;
                        }

                        // if current version is unapproved return it
                        if ($version->get('approved') == 0) {
                            $unapproved[] = $result;
                        }
                    }
                }
                return new ItemList($unapproved);
            case 'tree':
                $tree = [];
                if ($results = $this->pages('list', $filters, $clear)) {
                    $tree = $this->buildTree($results);
                }
                return $tree;
            case 'list':
            default:
                if (!($this->pages instanceof ItemList) || $clear) {
                    $tbl = new Tables\Page($this->db);
                    if ($results = $tbl->find($filters)) {
                        foreach ($results as $key => $result) {
                            $results[$key] = new Page($result);
                        }
                    }
                    $this->pages = new ItemList($results);
                }
                return $this->pages;
        }
    }

    /**
     * Reset all pages with new value for key
     *
     * @param      string  $key         Page key to reset
     * @param      string  $value       New value to set
     * @param      array   $filters     Filters passed to pages() method
     * @return     mixed
     */
    public function reset($key = 'home', $value = 0, $filters = [])
    {
        // get list of pages
        $pages = $this->pages('list', $filters);

        // reset each page
        foreach ($pages as $page) {
            $page->set($key, $value);
            $page->store();
        }
    }

    /**
     * Build Multi Dimensional Tree of Pages
     *
     * @param  [type] $results [description]
     * @return [type]          [description]
     */
    private function buildTree($pages)
    {
        // vars to hold the tree array
        $tmpTree = [];
        $tree    = [];

        // first loop organizes by parent
        foreach ($pages as $page) {
            if (!isset($tmpTree[$page->get('parent')])) {
                $tmpTree[$page->get('parent')] = [];
            }
            if (!isset($tmpTree[$page->get('id')])) {
                $tmpTree[$page->get('id')] = [];
            }

            // add our page to the parent array
            $tmpTree[$page->get('parent')][] = $page;
        }

        // second loop attaches children to parent
        foreach ($pages as $page) {
            $children = $tmpTree[$page->get('id')];
            $page->set('children', $children);
            $tree[$page->get('parent')][] = $page;
        }

        // only return base node tree if it exists
        if (count($tree) != 0) {
            return $tree[0];
        }
        return null;
    }
}
