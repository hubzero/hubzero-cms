<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Dashboard\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Facades\Date;

/**
 * Model class for dashboard preferences
 */
class Preference extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     */
    protected $namespace = 'xprofiles_dashboard';

    /**
     * Default order by for model
     *
     * @var string
     */
    public $orderBy = 'id';

    /**
     * Default order direction for select queries
     *
     * @var string
     */
    public $orderDir = 'asc';

    /**
     * Fields and their validation criteria
     *
     * @var array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'preferences' => 'notempty',
        'uidNumber'   => 'positive|nonzero',
    ];

    /**
     * Automatically fillable fields
     *
     * @var array<int, string>
     */
    public $always = [
        'modified',
    ];

    /**
     * Registry
     *
     * @var \Hubzero\Config\Registry|null
     */
    protected $preferences = null;

    /**
     * Generates automatic modified field value
     *
     * @param  array<mixed> $data The data being saved
     * @return string
     */
    public function automaticModified($data)
    {
        return Date::toSql();
    }

    /**
     * Retrieves one row loaded by an alias field
     *
     * @param  string $user_id The alias to load by
     * @return mixed
     */
    public static function oneByUser($user_id)
    {
        return self::blank()
            ->whereEquals('uidNumber', $user_id)
            ->row();
    }

    /**
     * Get the parent user associated with this entry
     *
     * @return \Hubzero\Database\Relationship\BelongsToOne
     */
    public function user()
    {
        return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
    }

    /**
     * Transform params
     *
     * @return \Hubzero\Config\Registry
     */
    public function transformPreferences()
    {
        if (!is_object($this->preferences)) {
            $this->preferences = new Registry($this->get('preferences'));
        }

        return $this->preferences;
    }

    /**
     * Save data
     *
     * @return bool
     */
    public function save()
    {
        if (!is_string($this->get('preferences'))) {
            $this->set('preferences', json_encode($this->get('preferences')));
        }

        return parent::save();
    }
}
