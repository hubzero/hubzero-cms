<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Module;

use Components\Groups\Tables;
use Hubzero\Base\Model;
use Lang;

/**
 * Group module menu model class
 */
class Menu extends Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Groups\\Tables\\ModuleMenu';

	/**
	 * Constructor
	 *
	 * @param   mixed $oid
	 * @return  void
	 */
	public function __construct($oid)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\ModuleMenu($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get page title
	 * 
	 * @return  string
	 */
	public function getPageTitle()
	{
		if ($this->get('pageid') == 0)
		{
			return Lang::txt('COM_GROUPS_PAGES_MODULE_INCLUDED_ON_ALL_PAGES');
		}

		if ($this->get('pageid') == -1)
		{
			return Lang::txt('COM_GROUPS_PAGES_MODULE_INCLUDED_ON_NO_PAGES');
		}

		// new group page
		$tbl = new Tables\Page($this->_db);

		// load page
		$tbl->load($this->get('pageid'));

		// return page title
		return $tbl->get('title');
	}

}
