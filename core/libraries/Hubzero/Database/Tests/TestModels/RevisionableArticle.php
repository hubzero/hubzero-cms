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
 * Test model with Revisionable trait (default config)
 */
class RevisionableArticle extends Relational
{
    use Revisionable;

    protected $table = 'revisionable_articles';
}
