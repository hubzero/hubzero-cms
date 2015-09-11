<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Component;

require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'grade.policies.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');

/**
 * Courses model class for grade book
 */
class GradePolicies extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\GradePolicies';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'gradepolicies';

	/**
	 * Constructor
	 *
	 * @param      integer $id  Resource ID
	 * @param      integer $sid Section ID
	 * @return     void
	 */
	public function __construct($oid, $sid=null)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new $this->_tbl_name($this->_db);

		if (is_numeric($oid))
		{
			// Check if this is the default section
			if (!is_null($sid))
			{
				$section = new Section($sid);

				if (!$section->get('is_default'))
				{
					$config  = Component::params('com_courses');
					$canEdit = $config->get('section_grade_policy', true);

					if (!$canEdit)
					{
						// We need to find the default section and use that grade policy
						$offering = new Offering($section->get('offering_id'));
						$default  = $offering->section('!!default!!');
						$oid      = $default->get('grade_policy_id');
					}
				}
			}
			$this->_tbl->load($oid);
		}
	}
}