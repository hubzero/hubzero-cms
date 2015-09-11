<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Models\Section;

use Components\Courses\Models\Base;

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'section.date.php');
require_once(dirname(__DIR__) . DS . 'base.php');

/**
 * Courses model class for a course
 */
class Date extends Base
{
	/**
	 * JTable class name
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

