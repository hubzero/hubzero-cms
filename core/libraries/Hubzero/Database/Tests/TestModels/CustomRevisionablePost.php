<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\Revisionable;

/**
 * Test model with custom revision configuration
 */
class CustomRevisionablePost extends Relational
{
    use Revisionable;

    protected $table = 'revisionable_posts';

    // Custom revision table
    /**
     * @var  string
     */
    protected $revisionTable = 'post_history';

    // Custom foreign key
    /**
     * @var  string
     */
    protected $revisionForeignKey = 'post_id';

    // Only track these fields
    protected $revisionable = ['title', 'body'];

    // Keep max 5 revisions
    /**
     * @var  int
     */
    protected $maxRevisions = 5;
}
