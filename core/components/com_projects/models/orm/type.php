<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Projects type model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Type extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     **/
    protected $namespace = 'project';

    /**
     * Default order by for model
     *
     * @var string
     */
    public $orderBy = 'id';

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
        'type' => 'notempty',
    ];

    /**
     * Registry
     *
     * @var  \Hubzero\Config\Registry|null
     */
    protected $paramsRegistry = null;

    /**
     * Transform params
     *
     * @return  \Hubzero\Config\Registry
     */
    public function transformParams()
    {
        if (!is_object($this->paramsRegistry)) {
            $this->paramsRegistry = new \Hubzero\Config\Registry($this->get('params'));
        }

        return $this->paramsRegistry;
    }
}
