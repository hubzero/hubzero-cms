<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Billboards\Models;

use Hubzero\Database\Relational;
use Hubzero\Facades\Component;

/**
 * Billboard database model
 *
 * @uses \Hubzero\Database\Relational
 *
 * Columns read as properties:
 *
 * @property  int     $collection_id
 * @property  string  $name
 * @property  string  $alias
 */
class Billboard extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     **/
    protected $namespace = 'billboards';

    /**
     * Fields and their validation criteria
     *
     * @var array<string, string|callable|array<string, mixed>>
     **/
    protected $rules = [
        'name' => 'notempty',
    ];

    /**
     * Automatically fillable fields
     *
     * @var array<int, string>
     **/
    public $initiate = [
        'ordering',
    ];

    /**
     * Automatically fillable fields
     *
     * @var array<int, string>
     **/
    public $always = [
        'alias',
    ];

    /**
     * Generates automatic ordering field
     *
     * @return int
     * @since  1.3.2
     **/
    public function automaticOrdering()
    {
        $instance = self::blank();
        $result = $instance
            ->select('MAX(ordering) + 1', 'ordering')
            ->whereEquals('collection_id', $this->collection_id)
            ->rows()
            ->first()
            ->ordering;

        return $result ? $result : 1;
    }

    /**
     * Generates automatic alias field
     *
     * @return string
     * @since  1.3.2
     **/
    public function automaticAlias()
    {
        return strtolower(preg_replace("/[^[:alnum:]]/ui", "", (($this->alias) ? $this->alias : $this->name)));
    }

    /**
     * Defines a belongs to one relationship with collection
     *
     * @return \Hubzero\Database\Relationship\BelongsToOne
     * @since  1.3.2
     **/
    public function collection()
    {
        return $this->belongsToOne(__NAMESPACE__ . '\\Collection');
    }

    /**
     * Gets the background image url
     *
     * @return string
     * @since  1.3.2
     */
    public function transformBackgroundImg()
    {
        $params = Component::params('com_billboards');
        $defaultLocation = DS . 'app' . DS . 'site' . DS . 'media' . DS . 'images' . DS . 'billboards' . DS;
        $base = $params->get('image_location', $defaultLocation);

        return DS . trim($base, DS) . DS . $this->get('background_img');
    }
}
