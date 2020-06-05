<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Plugins\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Filesystem;
use Lang;
use User;
use Date;

/**
 * Plugin extension model
 */
class Plugin extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'extension_id';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__extensions';

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
		'folder'  => 'notempty',
		'element' => 'notempty',
		'name'    => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified',
		'modified_by'
	);

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		if (!isset($data['modified'])
		 || !$data['modified']
		  || $data['modified'] == '0000-00-00 00:00:00')
		{
			$data['modified'] = Date::of('now')->toSql();
		}
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		if (!isset($data['modified_by']) || !$data['modified_by'])
		{
			$data['modified_by'] = User::get('id');
		}
		return $data['modified_by'];
	}

	/**
	 * Get all records
	 *
	 * @param   array  $columns
	 * @return  object
	 */
	public static function all($columns = null)
	{
		return parent::all()->whereEquals('type', 'plugin');
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
			$path = DS . 'plugins' . DS . $this->get('folder') . DS . $this->get('element') . DS . $this->get('element') . '.xml';

			$paths = array(
				'app'  => Filesystem::cleanPath(PATH_APP . $path),
				'core' => Filesystem::cleanPath(PATH_CORE . $path)
			);

			foreach ($paths as $p)
			{
				if (file_exists($p))
				{
					// Disable libxml errors and allow to fetch error information as needed
					libxml_use_internal_errors(true);

					$this->manifest = simplexml_load_file($p);
					break;
				}
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
		$file = 'plg_' . $this->get('folder') . '_' . $this->get('element') . ($system ? '.sys' : '');
		$path = $this->path();

		if ($path)
		{
			return Lang::load($file, $path, null, false, true);
		}

		return false;
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
	 * Get the installed path
	 *
	 * @return  string
	 */
	public function path()
	{
		if (!$this->get('folder') || !$this->get('element'))
		{
			return '';
		}
		return \Plugin::path($this->get('folder'), $this->get('element'));
	}

	/**
	 * Get a form
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/plugin.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('plugin', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$paths   = array();
		$paths[] = $this->path() . '/config/config.xml';
		$paths[] = $this->path() . '/' . $this->get('element') . '.xml';

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
			->whereEquals('folder', $this->get('folder'))
			->whereEquals('type', $this->get('type'));

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
}
