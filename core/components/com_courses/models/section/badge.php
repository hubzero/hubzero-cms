<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Section;

use Components\Courses\Models\Base;
use Components\Courses\Tables;
use stdClass;

require_once dirname(__DIR__) . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'section.badge.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'section.badge.criteria.php';

/**
 * Courses model class for badges
 */
class Badge extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\SectionBadge';

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
		$this->_db = \App::get('db');

		$this->_tbl = new $this->_tbl_name($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}

		// Get criteria text
		$criteria = new Tables\SectionBadgeCriteria($this->_db);
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
		$model->_db = \App::get('db');

		$model = new self();

		$model->_tbl = new $model->_tbl_name($model->_db);

		$model->_tbl->load(array('section_id'=>$id));

		// Get criteria text
		$criteria = new Tables\SectionBadgeCriteria($model->_db);
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
		$model->_db = \App::get('db');

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
			$criteria = new Tables\SectionBadgeCriteria($this->_db);
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
		$cconfig        = \Component::params('com_courses');
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
