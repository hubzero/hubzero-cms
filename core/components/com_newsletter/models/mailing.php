<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;

/**
 * Newsletter model for a mailing
 *
 * @property  int|string|null  $id
 * @property  int|string|null  $lid
 * @property-read  mixed  $newsletter  What newsletter() gives
 */
class Mailing extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = 'newsletter';

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
        'nid' => 'positive|nonzero',
    ];

    /**
     * Defines a belongs to one relationship between newsletter and mailing
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function newsletter()
    {
        return $this->belongsToOne(__NAMESPACE__ . '\\Newsletter', 'nid');
    }

    /**
     * Defines a belongs to one relationship between mailinglist and mailing
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function mailinglist()
    {
        return $this->belongsToOne(__NAMESPACE__ . '\\Mailinglist', 'lid');
    }

    /**
     * Get a list of recipients
     *
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function recipients()
    {
        return $this->oneToMany(__NAMESPACE__ . '\\Mailing\\Recipient', 'mid');
    }

    /**
     * Get a list of actions
     *
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function actions()
    {
        return $this->oneToMany(__NAMESPACE__ . '\\Mailing\\Recipient\\Action', 'mailingid');
    }
}
