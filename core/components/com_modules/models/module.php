<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Modules\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Filesystem;
use Lang;

include_once __DIR__ . '/menu.php';

/**
 * Module extension model
 */
class Module extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Configuration registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * XML manifest
	 *
	 * @var  object
	 */
	protected $manifest = null;

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'    => 'notempty',
		'position' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'publish_up',
		'publish_down'
	);

	/**
	 * The path to the installed files
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * Generates automatic publish_up field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishUp($data)
	{
		if (!isset($data['publish_up'])
		 || !$data['publish_up']
		 || $data['publish_up'] == '0000-00-00 00:00:00')
		{
			$data['publish_up'] = null;
		}

		return $data['publish_up'];
	}

	/**
	 * Generates automatic publish_down field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishDown($data)
	{
		if (!isset($data['publish_down'])
		 || !$data['publish_down']
		 || $data['publish_down'] == '0000-00-00 00:00:00')
		{
			$data['publish_down'] = null;
		}

		return $data['publish_down'];
	}

	/**
	 * Get the XML maniest
	 *
	 * @return  mixed  XML object or null
	 */
	public function transformXml()
	{
		if (is_null($this->manifest))
		{
			$paths = array();

			if (substr($this->get('module'), 0, 4) == 'mod_')
			{
				$paths[] = $this->path() . DS . substr($this->get('module'), 4) . '.xml';
			}

			$paths[] = $this->path() . DS . $this->get('module') . '.xml';

			foreach ($paths as $file)
			{
				if (file_exists($file))
				{
					// Disable libxml errors and allow to fetch error information as needed
					libxml_use_internal_errors(true);

					$this->manifest = simplexml_load_file($file);
					break;
				}
			}

			if (!$this->manifest)
			{
				$this->manifest = new \stdClass;
				$this->manifest->name = $this->get('module');
				$this->manifest->description = '';
			}

			if ($this->get('module') == '')
			{
				$this->manifest->name        = 'custom';
				$this->manifest->module      = 'custom';
				$this->manifest->description = 'Custom created module, using Module Manager New function';
			}
		}

		return $this->manifest;
	}

	/**
	 * Load the language file for the plugin
	 *
	 * @param   boolean  $system  Load the system language file?
	 * @return  boolean
	 */
	public function loadLanguage($system = false)
	{
		$file = $this->get('module') . ($system ? '.sys' : '');

		return Lang::load($file, $this->path(), null, false, true);
	}

	/**
	 * Get params as a Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}
		return $this->paramsRegistry;
	}

	/**
	 * Get a form
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/module.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('module', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$paths = array();

		$paths[] = $this->path() . DS . 'config' . DS . 'config.xml';

		if (substr($this->get('module'), 0, 4) == 'mod_')
		{
			$paths[] = $this->path() . DS . substr($this->get('module'), 4) . '.xml';
		}

		$paths[] = $this->path() . DS . $this->get('module') . '.xml';

		foreach ($paths as $file)
		{
			if (file_exists($file))
			{
				// Get the plugin form.
				if (!$form->loadFile($file, false, '//config'))
				{
					$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
				}
				break;
			}
		}

		$data = $this->toArray();
		$data['params'] = $this->params->toArray();

		$form->bind($data);

		return $form;
	}

	/**
	 * Method to change the title.
	 *
	 * @param   string   $title        The title.
	 * @param   string   $position     The position.
	 * @return  array    Contains the modified title.
	 */
	public function generateNewTitle($title, $position)
	{
		// Alter the title & alias
		$models = self::all()
			->whereEquals('position', $position)
			->whereEquals('title', $title)
			->total();

		for ($i = 0; $i < $models; $i++)
		{
			$title = self::increment($title);
		}

		return array($title);
	}

	/**
	 * Increments a trailing number in a string.
	 *
	 * Used to easily create distinct labels when copying objects. The method has the following styles:
	 *
	 * default: "Label" becomes "Label (2)"
	 * dash:    "Label" becomes "Label-2"
	 *
	 * @param   string   $string  The source string.
	 * @param   string   $style   The the style (default|dash).
	 * @param   integer  $n       If supplied, this number is used for the copy, otherwise it is the 'next' number.
	 * @return  string   The incremented string.
	 */
	protected static function increment($string, $style = 'default', $n = 0)
	{
		$incrementStyles = array(
			'dash' => array(
				'#-(\d+)$#',
				'-%d'
			),
			'default' => array(
				array('#\((\d+)\)$#', '#\(\d+\)$#'),
				array(' (%d)', '(%d)'),
			),
		);

		$styleSpec = isset($incrementStyles[$style]) ? $incrementStyles[$style] : $incrementStyles['default'];

		// Regular expression search and replace patterns.
		if (is_array($styleSpec[0]))
		{
			$rxSearch  = $styleSpec[0][0];
			$rxReplace = $styleSpec[0][1];
		}
		else
		{
			$rxSearch = $rxReplace = $styleSpec[0];
		}

		// New and old (existing) sprintf formats.
		if (is_array($styleSpec[1]))
		{
			$newFormat = $styleSpec[1][0];
			$oldFormat = $styleSpec[1][1];
		}
		else
		{
			$newFormat = $oldFormat = $styleSpec[1];
		}

		// Check if we are incrementing an existing pattern, or appending a new one.
		if (preg_match($rxSearch, $string, $matches))
		{
			$n = empty($n) ? ($matches[1] + 1) : $n;
			$string = preg_replace($rxReplace, sprintf($oldFormat, $n), $string);
		}
		else
		{
			$n = empty($n) ? 2 : $n;
			$string .= sprintf($newFormat, $n);
		}

		return $string;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		$pk = $this->get('id');

		$result = parent::destroy();

		// Attempt to delete the record
		if ($result)
		{
			// Delete the menu assignments
			$db = App::get('db');
			$query = $db->getQuery()
				->delete('#__modules_menu')
				->whereEquals('moduleid', (int)$pk);
			$db->setQuery((string)$query);
			$result = $db->query();
		}

		return $result;
	}

	/**
	 * Get the installed path
	 *
	 * @return  string
	 */
	public function path()
	{
		if (is_null($this->path))
		{
			$this->path = '';

			if ($module = $this->get('module'))
			{
				$paths = array();

				if (substr($module, 0, 4) == 'mod_')
				{
					$path = '/modules/' . substr($module, 4) . '/' . substr($module, 4) . '.php';

					$paths[] = PATH_APP . $path;
					$paths[] = PATH_CORE . $path;
				}

				$path = '/modules/' . $module . '/' . $module . '.php';

				$paths[] = PATH_APP . $path;
				$paths[] = PATH_CORE . $path;

				foreach ($paths as $file)
				{
					if (file_exists($file))
					{
						$this->path = dirname($file);
						break;
					}
				}
			}
		}

		return $this->path;
	}

	/**
	 * Duplicate a record and menu assignments
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function duplicate()
	{
		$pk = $this->get('id');

		$this->set('id', 0);

		// Alter the title.
		$m = null;
		if (preg_match('#\((\d+)\)$#', $this->get('title'), $m))
		{
			$this->set('title', preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $this->get('title')));
		}
		else
		{
			$this->set('title', $this->get('title') . ' (2)');
		}

		// Unpublish duplicate module
		$this->set('published', 0);

		if (!$this->save())
		{
			return false;
		}

		$db = App::get('db');
		$query = $db->getQuery()
			->select('menuid')
			->from('#__modules_menu')
			->whereEquals('moduleid', (int)$pk);

		$db->setQuery((string)$query->toString());
		$rows = $db->loadColumn();

		foreach ($rows as $menuid)
		{
			$tuples[] = '(' . (int) $this->get('id') . ',' . (int) $menuid . ')';
		}

		if (!empty($tuples))
		{
			// Module-Menu Mapping: Do it in one query
			$query = 'INSERT INTO `#__modules_menu` (moduleid, menuid) VALUES ' . implode(',', $tuples);
			$db->setQuery($query);

			if (!$db->query())
			{
				$this->addError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 * @return  bool     True on success.
	 */
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all()
			->whereEquals('position', $this->get('position'));

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('ordering', '<', (int) $this->get('ordering'));
			$query->order('ordering', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('ordering', '>', (int) $this->get('ordering'));
			$query->order('ordering', 'asc');
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$query->whereRaw($where);
		}

		// Select the first row with the criteria.
		$row = $query->ordered()->row();

		// If a row is found, move the item.
		if ($row->get($this->pk))
		{
			$prev = $this->get('ordering');

			// Update the ordering field for this instance to the row's ordering value.
			$this->set('ordering', (int) $row->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}

			// Update the ordering field for the row to this instance's ordering value.
			$row->set('ordering', (int) $prev);

			// Check for a database error.
			if (!$row->save())
			{
				return false;
			}
		}
		else
		{
			// Update the ordering field for this instance.
			$this->set('ordering', (int) $this->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array  $pks    An array of primary key ids.
	 * @param   array  $order  An array of order values.
	 * @return  bool
	 */
	public static function saveorder($pks = null, $order = null)
	{
		if (empty($pks))
		{
			return false;
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$model = self::oneOrFail((int) $pk);

			if ($model->get('ordering') != $order[$i])
			{
				$model->set('ordering', $order[$i]);

				if (!$model->save())
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get menu assignments
	 *
	 * @return  array
	 */
	public function menuAssigned()
	{
		/*$db = App::get('db');
		$db->setQuery(
			'SELECT menuid' .
			' FROM #__modules_menu' .
			' WHERE moduleid = '.$this->get('id')
		);
		return $db->loadColumn();*/

		return Menu::all()
			->whereEquals('moduleid', (int)$this->get('id'))
			->rows()
			->fieldsByKey('menuid');
	}

	/**
	 * Determine how the assignment
	 *
	 * @return  array
	 */
	public function menuAssignment()
	{
		// Determine the page assignment mode.
		$assigned = $this->menuAssigned();

		if ($this->isNew())
		{
			// If this is a new module, assign to all pages.
			$assignment = 0;
		}
		elseif (empty($assigned))
		{
			// For an existing module it is assigned to none.
			$assignment = '-';
		}
		else
		{
			if ($assigned[0] > 0)
			{
				$assignment = +1;
			}
			elseif ($assigned[0] < 0)
			{
				$assignment = -1;
			}
			else
			{
				$assignment = 0;
			}
		}

		return $assignment;
	}

	/**
	 * Save menu assignments for a module
	 *
	 * @param   integer  $assignment
	 * @param   array    $assigned
	 * @return  bool
	 */
	public function saveAssignment($assignment, $assigned)
	{
		$assignment = $assignment ? $assignment : 0;

		// Delete old module to menu item associations
		if (!Menu::destroyForModule($this->get('id')))
		{
			$this->addError('Failed to remove previous menu assignments.');
			return false;
		}

		// If the assignment is numeric, then something is selected (otherwise it's none).
		if (is_numeric($assignment))
		{
			// Variable is numeric, but could be a string.
			$assignment = (int) $assignment;

			// Logic check: if no module excluded then convert to display on all.
			if ($assignment == -1 && empty($assigned))
			{
				$assignment = 0;
			}

			// Check needed to stop a module being assigned to `All`
			// and other menu items resulting in a module being displayed twice.
			if ($assignment === 0)
			{
				// assign new module to `all` menu item associations
				$menu = Menu::blank()->set(array(
					'moduleid' => $this->get('id'),
					'menuid'   => 0
				));

				if (!$menu->save())
				{
					$this->addError('Failed saving: ' . $menu->getError());
					return false;
				}
			}
			elseif (!empty($assigned))
			{
				// Get the sign of the number.
				$sign = $assignment < 0 ? -1 : +1;

				// Preprocess the assigned array.
				$tuples = array();
				foreach ($assigned as &$pk)
				{
					$menu = Menu::blank()->set(array(
						'moduleid' => $this->get('id'),
						'menuid'   => ((int) $pk * $sign)
					));

					if (!$menu->save())
					{
						$this->addError('More failed: ' . $menu->getError());
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Checks back in the current model
	 *
	 * @return  void
	 **/
	public function checkin()
	{
		if (!$this->isNew())
		{
			$this->set('checked_out', 0)
			     ->set('checked_out_time', null)
			     ->save();
		}
	}
}
