<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Connection provider model
 *
 * @uses  \Hubzero\Database\Relational
 *
 * @property  string|null  $alias
 * @property  string|null  $name
 */
class Provider extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     **/
    protected $namespace = 'projects_connection';

    /**
     * Default order by for model
     *
     * @var string
     */
    public $orderBy = 'name';

    /**
     * Default order direction for select queries
     *
     * @var  string
     */
    public $orderDir = 'asc';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'name' => 'notempty',
    ];

    /**
     * Automatically fillable fields
     *
     * @var  array<int, string>
     */
    public $always = [
        'alias',
    ];

    /**
     * Generates automatic owned by field value
     *
     * @param   array<mixed>   $data  the data being saved
     * @return  string
     */
    public function automaticAlias($data)
    {
        $alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['name']);
        $alias = strip_tags($alias);

        return (string) preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
    }
}
