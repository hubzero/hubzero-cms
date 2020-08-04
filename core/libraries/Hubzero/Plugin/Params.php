<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Class for custom plugin parameters
 */
class Params extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'plugin';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__plugin_params';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'folder';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'object_id' => 'positive|nonzero',
		'folder'    => 'notempty',
		'element'   => 'notempty'
	);

	/**
	 * Load a record and binf to $this
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  boolean  True on success
	 */
	public static function oneByPlugin($oid=null, $folder=null, $element=null)
	{
		return self::all()
			->whereEquals('object_id', (int) $oid)
			->whereEquals('folder', (int) $folder)
			->whereEquals('element', (int) $element)
			->row();
	}

	/**
	 * Get the custom parameters for a plugin
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  object
	 */
	public static function getCustomParams($oid=null, $folder=null, $element=null)
	{
		$result = self::oneByPlugin($oid, $folder, $element);

		return new Registry($result->get('params'));
	}

	/**
	 * Get the default parameters for a plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin name
	 * @return  object
	 */
	public static function getDefaultParams($folder=null, $element=null)
	{
		$plugin = \Plugin::byType($folder, $element);

		return new Registry($plugin->params);
	}

	/**
	 * Get the parameters for a plugin
	 * Merges default params and custom params (take precedence)
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  object
	 */
	public static function getParams($oid=null, $folder=null, $element=null)
	{
		$custom = self::getCustomParams($oid, $folder, $element);

		$params = self::getDefaultParams($folder, $element);
		$params->merge($custom);

		return $params;
	}
}
