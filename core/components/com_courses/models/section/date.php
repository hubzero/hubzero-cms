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
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_tbl_name = '\\Components\\Courses\\Tables\\SectionDate';

    /**
     * Object scope
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_scope = 'section_date';
}
