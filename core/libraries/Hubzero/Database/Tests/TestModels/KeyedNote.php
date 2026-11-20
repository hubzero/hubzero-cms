<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for belongsToOne() key defaults: primary key note_id
 *
 * @property-read  PlainOwner  $owner
 */
class KeyedNote extends Relational
{
    protected $table = 'key_notes';
    protected $pk = 'note_id';
    protected $namespace = '';

    /**
     * The owner, a model whose primary key is 'id'
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function owner()
    {
        return $this->belongsToOne(PlainOwner::class, 'owner_id');
    }
}
