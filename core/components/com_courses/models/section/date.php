<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Section;

use Components\Courses\Models\Base;

/**
 * Courses model class for a course
 */
class Date extends Base
{
    /**
     * Table class name
     *
     * @var string
     */
    protected $tableClass = '\\Components\\Courses\\Tables\\SectionDate';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Courses\Tables\SectionDate|null
     */
    protected $table = null;

    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'section_date';
}
