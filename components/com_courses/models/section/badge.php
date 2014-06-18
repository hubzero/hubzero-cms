<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.badge.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.badge.criteria.php');

/**
 * Courses model class for badges
 */
class CoursesModelSectionBadge extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableSectionBadge';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'sectionbadge';

	/**
	 * Constructor
	 *
	 * @param      integer $id  Resource ID or alias
	 * @return     void
	 */
	public function __construct($oid=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new $this->_tbl_name($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}

		// Get criteria text
		$criteria = new CoursesTableSectionBadgeCriteria($this->_db);
		$criteria->load($this->get('criteria_id'));

		if ($criteria->get('text'))
		{
			$this->set('criteria_text', $criteria->get('text'));
		}
	}

	/**
	 * Load by section id
	 *
	 * @param      integer $id  Section id
	 * @return     void
	 */
	public static function loadBySectionId($id)
	{
		$model = new stdClass();
		$model->_db = JFactory::getDBO();

		$model = new self();

		$model->_tbl = new $model->_tbl_name($model->_db);

		$model->_tbl->load(array('section_id'=>$id));

		// Get criteria text
		$criteria = new CoursesTableSectionBadgeCriteria($model->_db);
		$criteria->load($model->get('criteria_id'));

		if ($criteria->get('text'))
		{
			$model->set('criteria_text', $criteria->get('text'));
		}

		return $model;
	}

	/**
	 * Load by provider badge id
	 *
	 * @param      integer $id  Provider badge id
	 * @return     void
	 */
	public function loadByProviderBadgeId($id)
	{
		$model = new stdClass();
		$model->_db = JFactory::getDBO();

		$model = new self();

		$model->_tbl = new $model->_tbl_name($model->_db);

		$model->_tbl->load(array('provider_badge_id'=>$id));

		return $model;
	}

	/**
	 * Store
	 *
	 * @param      bool $check
	 * @return     void
	 */
	public function store($check=true)
	{
		// See if we should save criteria text as well
		if ($this->get('criteria_text_new'))
		{
			// We'll always save a new entry if the text is changing
			$criteria = new CoursesTableSectionBadgeCriteria($this->_db);
			$criteria->set('text', $this->get('criteria_text_new'));
			$criteria->set('section_badge_id', $this->get('id'));
			$criteria->store();

			$this->set('criteria_id', $criteria->get('id'));
		}

		return parent::store($check);
	}

	/**
	 * Get badge claim url
	 *
	 * @return     void
	 */
	public function getClaimUrl()
	{
		return $this->getUrl('Claim');
	}

	/**
	 * Get badge denied url
	 *
	 * @return     void
	 */
	public function getDeniedUrl()
	{
		return $this->getUrl('Denied');
	}

	/**
	 * Get badges url
	 *
	 * @return     void
	 */
	public function getBadgesUrl()
	{
		return $this->getUrl('Badges');
	}

	/**
	 * Get url
	 *
	 * @return     void
	 */
	private function getUrl($type='Claim')
	{
		$cconfig        = JComponentHelper::getParams('com_courses');
		$request_type   = $cconfig->get('badges_request_type', 'oauth');
		$badgesHandler  = new \Hubzero\Badges\Wallet(strtoupper($this->get('provider_name')), $request_type);
		$badgesProvider = $badgesHandler->getProvider();

		return $badgesProvider->getUrl($type);
	}

	/**
	 * Check to see if a badge meets all requirements of being available
	 *
	 * @return     bool
	 */
	public function isAvailable()
	{
		if ($this->get('published') && $this->get('img_url') && $this->get('provider_badge_id'))
		{
			return true;
		}

		return false;
	}
}
