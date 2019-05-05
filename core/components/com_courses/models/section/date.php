<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Section;

use Components\Courses\Models\Base;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'section.date.php';
require_once dirname(__DIR__) . DS . 'base.php';

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
	protected $_tbl_name = '\\Components\\Courses\\Tables\\SectionDate';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'section_date';
}
