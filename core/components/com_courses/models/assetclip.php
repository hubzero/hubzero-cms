<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Config\Registry;
use Lang;


require_once dirname(__DIR__) . DS . 'tables' . DS . 'asset.clip.php';
require_once __DIR__ . DS . 'base.php';

/**
 * Courses model class for an asset clip
 */
class Assetclip extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\AssetClip';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'asset_clip';

	/**
	 * Registry
	 *
	 * @var object
	 */
	private $_params = null;

	/**
	 * Constructor
	 *
	 * @param   mixed $oid Integer, array, or object
	 * @return  void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);
	}

	/**
	 * Returns a property of the params
	 *
	 * @param   string $property The name of the property
	 * @param   mixed  $default  The default value
	 * @return  mixed  The value of the property
  */
	public function params($key, $default=null)
	{
		if (!($this->_params instanceof Registry))
		{
			$this->_params = new Registry($this->get('params'));
		}
		return $this->_params->get($key, $default);
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string $property The name of the property
	 * @param   mixed  $default  The default value
	 * @return  mixed  The value of the property
  */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property))
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property}))
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Store changes to this entry
	 *
	 * @param   boolean $check Perform data validation check?
	 * @return  boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$value = parent::store($check);
		return $value;
	}

	/**
	 * Delete an entry 
	 *
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		return parent::delete();
	}
}
