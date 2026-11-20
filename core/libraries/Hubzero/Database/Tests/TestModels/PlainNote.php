<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for belongsToOne() key defaults: primary key id
 *
 * @property-read  KeyedOwner  $owner
 */
class PlainNote extends Relational
{
    protected $table = 'plain_notes';
    protected $pk = 'id';
    protected $namespace = '';

    /**
     * The owner, a model whose primary key is 'owner_key'
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function owner()
    {
        return $this->belongsToOne(KeyedOwner::class, 'owner_key');
    }
}
