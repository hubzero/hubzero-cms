<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page\Version;

use Components\Groups\Models\Page;
use Components\Groups\Models\Page\Version;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use Request;
use Hubzero\Facades\App;

/**
 * Group page version archive model class
 *
 * @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
 */
class Archive extends Model
{
    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $versions = null;

    /**
     * Version Count
     *
     * @var int
     */
    private $versions_count = null;

    /**
     * Constructor
     *
     * @return     void
     */
    public function __construct()
    {
        // create database object
        $this->_db = App::get('db');
    }

    /**
     * Get a list of group page versions
     *
     * @param      string  $rtrn    What data to return
     * @param      array   $filters Filters to apply to data retrieval
     * @param      boolean $boolean Clear cached data?
     * @return     mixed
     */
    public function versions($rtrn = 'list', $filters = [], $clear = false)
    {
        $tbl = new Tables\PageVersion($this->_db);

        switch (strtolower($rtrn)) {
            case 'count':
                if (!isset($this->versions_count)) {
                    $this->versions_count = $tbl->count($filters);
                }
                return (int) $this->versions_count;
                break;
            case 'list':
            default:
                if (!($this->versions instanceof ItemList) || $clear) {
                    // make sure we have page id
                    if (!isset($filters['pageid'])) {
                        $filters['pageid'] = 0;
                    }

                    if ($results = $tbl->find($filters)) {
                        foreach ($results as $key => $result) {
                            $results[$key] = new Version($result);
                        }
                    }
                    $this->versions = new ItemList($results);
                }
                return $this->versions;
                break;
        }
    }
}
