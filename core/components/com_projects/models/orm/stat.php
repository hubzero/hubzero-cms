<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Projects Stat model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Stat extends Relational
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
     * @var  string
     */
    public $orderBy = 'processed';

    /**
     * Default order direction for select queries
     *
     * @var  string
     */
    public $orderDir = 'desc';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'stats' => 'notempty',
    ];

    /**
     * Automatically fillable fields
     *
     * @var  array<int, string>
     */
    public $always = [
        'processed',
    ];

    /**
     * Generates automatic priority field value
     *
     * @param   array<mixed>   $data  the data being saved
     * @return  string
     */
    public function automaticProcessed($data)
    {
        if (!isset($data['processed']) || !$data['processed'] || $data['processed'] == '0000-00-00 00:00:00') {
            $data['processed'] = null;
        }

        return $data['processed'];
    }
}
