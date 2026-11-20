<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @var class-string<\Components\Courses\Tables\SectionDate>
     */
    protected $tableClass = '\\Components\\Courses\\Tables\\SectionDate';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Courses\Tables\SectionDate
     */
    protected $table;

    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'section_date';
}
