<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr;

use Hubzero\Database\Relational;
use Hubzero\Facades\App;

/**
 * Database model for search hub types
 *
 * @uses  \Hubzero\Database\Relational
 */
class HubType extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     **/
    protected $namespace = 'search';

    /**
     * Default order by for model
     *
     * @var  string
     **/
    public $orderBy = 'id';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     **/
    protected $rules = [
        //'type'  => 'notempty',
        //'title' => 'notempty'
    ];

    /**
     * Automatic fields to populate every time a row is created
     *
     * @var  array<int, string>
     **/
    public $initiate = [
        'created_by',
        'created',
    ];

    /**
     * Get structure for a type
     *
     * @return  mixed  Iterable
     */
    public function structure()
    {
        $classpath = $this->get('class_path');

        if (!str_contains($classpath, 'Tables')) {
            $model = new $classpath();
        } else {
            $database = App::get('db');
            $model = new $classpath($database);
        }
        $modelStructure = null;
        // A model further down either family is read the same way
        if ($model instanceof \Hubzero\Database\Relational) {
            $modelStructure = $model->getStructure()->getTableColumns($model->getTableName());
        } elseif ($model instanceof \Hubzero\Base\Model) {
            $modelStructure = $model->toArray();
        } elseif (isset($database) && method_exists($model, 'getFields')) {
            $modelStructure = $model->getFields();
        }

        return $modelStructure;
    }
}
