<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model for the tests of what a column can hold
 *
 * It describes its columns the way MySQL does, whatever backend runs the
 * test: SQLite calls varchar and datetime columns TEXT and holds anything.
 */
class FitPost extends Relational
{
    protected $table = 'fit_posts';

    /**
     * The columns as MySQL describes them
     *
     * @return  array<mixed>
     */
    public function getTableColumns()
    {
        $column = fn($name, $type, $null, $default = null) => [
            'name' => $name, 'type' => $type, 'allownull' => $null, 'default' => $default, 'pk' => $name === 'id',
        ];

        return [
            'id'           => $column('id', 'int(11) unsigned', false),
            'title'        => $column('title', 'varchar(20)', false, ''),
            'count'        => $column('count', 'int(11)', false, '0'),
            'score'        => $column('score', 'int(11)', true),
            'published_at' => $column('published_at', 'datetime', true),
        ];
    }
}
