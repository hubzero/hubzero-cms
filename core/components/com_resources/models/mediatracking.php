<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;

/**
 * Media tracking model
 *
 * @uses  \Hubzero\Database\Relational
 *
 * @property  int|string|null  $current_position
 * @property  int|string|null  $object_duration
 */
class MediaTracking extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = 'media';

    /**
     * The table to which the class pertains
     *
     * This will default to #__{namespace}_{modelName} unless otherwise
     * overwritten by a given subclass. Definition of this property likely
     * indicates some derivation from standard naming conventions.
     *
     * @var  string
     */
    protected $table = '#__media_tracking';

    /**
     * Default order by for model
     *
     * @var  string
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
        'object_id'   => 'positive|nonzero',
        'object_type' => 'notempty',
        'session_id'  => 'notempty',
        'ip_address'  => 'notempty',
    ];

    /**
     * Get a record by its doi
     *
     * @param   integer  $user_id      User ID
     * @param   integer  $object_id    Object ID
     * @param   string   $object_type  Object type
     * @return  static
     */
    public static function oneByUserAndResource($user_id, $object_id, $object_type = 'resource')
    {
        return self::all()
            ->whereEquals('user_id', $user_id)
            ->whereEquals('object_id', $object_id)
            ->whereEquals('object_type', $object_type)
            ->row();
    }
}
