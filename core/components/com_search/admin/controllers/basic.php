<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;

if (!function_exists('stem'))
{
	/**
	 * Stem a string
	 *
	 * @param  string $str
	 * @return string
	 */
	function stem($str)
	{
		return $str;
	}
}

foreach (array('request', 'result', 'terms', 'authorization', 'documentmetadata') as $mdl)
{
	require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'basic' . DS . $mdl . '.php';
}
foreach (array('assoc', 'assoclist', 'assocscalar', 'blank', 'set', 'sql') as $mdl)
{
	require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'basic' . DS . 'result' . DS . $mdl . '.php';
}

/**
 * Search controller class
 */
class Basic extends AdminController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->display();
	}
}
