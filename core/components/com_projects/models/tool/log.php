<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Lang;

/**
 * Project Tool Log model
 */
class Log extends Model
{
	/**
	 * Table class name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolLog';

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $config = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\ToolLog($this->_db);
	}

	/**
	 * Returns a reference to a log model
	 *
	 * @param   mixed  $oid  object ID
	 * @return  object
	 */
	public static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Update Parent Name
	 *
	 * @param   integer  $id
	 * @param   string   $name
	 * @return  boolean
	 */
	public function updateParentName($id=null, $name=null)
	{
		if (!$id || !$name)
		{
			return false;
		}
		return $this->_tbl->updateParentName($id, $name);
	}

	/**
	 * getLastUpdate
	 *
	 * @param   boolean  $statusChange
	 * @return  object
	 */
	public function getLastUpdate($statusChange = false)
	{
		return $this->_tbl->getLastUpdate(
			$this->get('instance_id'),
			$this->get('parent_name'),
			$this->get('parent_id'),
			$statusChange
		);
	}

	/**
	 * Get item history
	 *
	 *
	 * @param   array   $filters
	 * @return  object  list
	 */
	public function getHistory($filters = array())
	{
		return $this->_tbl->getHistory(
			$this->get('parent_name'),
			$this->get('parent_id'),
			$this->get('instance_id'),
			$filters
		);
	}

	/**
	 * Determine changes
	 *
	 * @param   object  $old  Tool model
	 * @param   object  $new  Tool model
	 * @return  string
	 */
	public function whatChanged($old, $new)
	{
		$log = '';

		// Name change
		if ($old->get('name') != $new->get('name'))
		{
			$log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_NAME'));
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM');
			$log .= ' &ldquo;' . $old->get('name') . '&rdquo; ';
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO');
			$log .= ' &ldquo;' . $new->get('name') . '&rdquo; ';
			$log .= "\n";
		}

		// Title change
		if ($old->get('title') != $new->get('title'))
		{
			$log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_TITLE'));
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM');
			$log .= ' &ldquo;' . $old->get('title') . '&rdquo; ';
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO');
			$log .= ' &ldquo;' . $new->get('title') . '&rdquo; ';
			$log .= "\n";
		}

		// Dev area access change
		if ($old->get('opendev') != $new->get('opendev'))
		{
			$log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_DEV_AREA_ACCESS'));
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM') . ' ';
			$log .= $old->get('opendev') ? Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_SHARED') : Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_RESTRICTED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO') . ' ';
			$log .= $new->get('opendev') == 1 ? Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_SHARED') : Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_RESTRICTED');
			$log .= "\n";
		}

		return $log;
	}

	/**
	 * Get the actor
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function actor($property=null)
	{
		if (!isset($this->_actor) || !($this->_creator instanceof \Hubzero\User\User))
		{
			$this->_actor = \User::getInstance($this->get('actor'));
		}
		if ($property)
		{
			return $this->_actor->get($property);
		}
		return $this->_actor;
	}
}
