<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use Hubzero\Facades\App;

/**
 * Group page category model class
 *
 */
class Category extends Model
{
    /**
     * Table object
     *
     * @var object
     */
    protected $table = null;

    /**
     * Table name
     *
     * @var string
     */
    protected $tableClass = '\\Components\\Groups\\Tables\\PageCategory';

    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $pages = null;

    /**
     * Page count
     *
     * @var string|null
     */
    private $pages_count = null;

    /**
     * Constructor
     *
     * @param   mixed  $oid  Integer, array, or object
     * @return  void
     */
    public function __construct($oid = null)
    {
        // create database object
        $this->db = App::get('db');

        // create page cateogry table object
        $this->table = new $this->tableClass($this->db);

        // load object
        if (is_numeric($oid)) {
            $this->table->load($oid);
        } elseif (is_object($oid) || is_array($oid)) {
            $this->bind($oid);
        }
    }

    /**
     * Get pages in this category
     *
     * @param   string   $rtrn   What do we want back
     * @param   boolean  $clear  Fetch an updated list
     * @return  object|int   \Hubzero\Base\ItemList
     */
    public function getPages($rtrn = 'list', $clear = false)
    {
        // create page table
        $tbl = new Tables\Page($this->db);

        // build array of filters
        $filters = [
            'gidNumber' => $this->get('gidNumber'),
            'category'  => $this->get('id'),
            'state'     => [0, 1],
        ];

        switch (strtolower($rtrn)) {
            case 'count':
                if (!isset($this->pages_count)) {
                    $this->pages_count = $tbl->count($filters);
                }
                return (int) $this->pages_count;
            case 'list':
            default:
                if (!($this->pages instanceof ItemList) || $clear) {
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
}
