<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Comment model that always fails on save (for transaction rollback tests)
 */
class CascadeFailingCommentSave extends Relational
{
    protected $table = 'cascade_comments';
    protected $namespace = '';

    public function save()
    {
        $this->addError('Forced cascade save failure');
        return false;
    }
}
