<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredmember;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Component;
use User;

/**
 * Module class for displaying featured members
 */
class Helper extends Module
{
	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

		$database = \App::get('db');
		$this->row = null;

		// Randomly choose one
		$filters = array(
			'limit'      => 1,
			'show'       => trim($this->params->get('show')),
			'start'      => 0,
			'sortby'     => "RAND()",
			'search'     => '',
			'authorized' => false
		);
		if ($min = $this->params->get('min_contributions'))
		{
			$filters['contributions'] = $min;
		}

		$query = "SELECT id FROM `#__users` WHERE `block`=0 ORDER BY RAND() LIMIT 1";
		$db->setQuery($query);

		// Load their bio
		$this->row = User::oneOrNew($row->loadResult());

		if (trim(strip_tags($this->row->get('bio'))) == '')
		{
			return '';
		}

		// Did we have a result to display?
		if ($this->row)
		{
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			$config = Component::params('com_members');

			$rparams = new Registry($this->row->get('params'));
			$this->params = $config;
			$this->params->merge($rparams);

			require $this->getLayoutPath();
		}
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
