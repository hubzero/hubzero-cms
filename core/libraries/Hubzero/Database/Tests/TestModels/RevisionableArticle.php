<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
