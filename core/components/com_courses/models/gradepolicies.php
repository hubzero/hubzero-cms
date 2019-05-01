<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Component;

require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'grade.policies.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php';

/**
 * Courses model class for grade book
 */
class GradePolicies extends Base
{
	/**
	 * Table class name
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
