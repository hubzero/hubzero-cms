<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Filesystem;
use Lang;

/**
 * Plugin extension model
 */
class Extension extends Relational
{
	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'extension_id';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'element' => 'notempty',
		'name'    => 'notempty'
	);

	/**
	 * Find a record by name
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function oneByName($name)
	{
		return self::all()
			->whereEquals('name', $name)
			->row();
	}

	/**
	 * Find a record by element
	 *
	 * @param   string  $element
	 * @return  object
	 */
	public static function oneByElement($element)
	{
		return self::all()
			->whereEquals('element', $element)
			->row();
	}

	/**
	 * Load the language file for the plugin
	 *
	 * @param   boolean  $system  Load the system language file?
	 * @return  boolean
	 */
	public function loadLanguage($system = false)
	{
		switch ($this->get('type'))
		{
			case 'plugin':
				$file = 'plg_' . $this->get('folder') . '_' . $this->get('element') . ($system ? '.sys' : '');
				$path = '/plugins/' . $this->get('folder') . '/' . $this->get('element');
			break;

			case 'module':
				$file = $this->get('element') . ($system ? '.sys' : '');
				$path = '/modules/' . $this->get('element');
			break;

			case 'component':
				$file = $this->get('element') . ($system ? '.sys' : '');
				$path = '/components/' . $this->get('element') . '/admin';
			break;

			case 'template':
				$file = $this->get('element') . ($system ? '.sys' : '');
				$path = '/templates/' . $this->get('element');
			break;
		}

		return (Lang::load($file, PATH_APP . $path, null, false, true) || Lang::load($file, PATH_CORE . $path, null, false, true));
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
		$file = __DIR__ . '/forms/application.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('com_config.application', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$data = $this->toArray();
		$data['params'] = $this->params->toArray();

		$form->bind($data);

		return $form;
	}

	/**
	 * Save data
	 *
	 * @return  bool
	 */
	public function save()
	{
		if (is_array($this->get('params')))
		{
			$params = new Registry($this->get('params'));

			$this->set('params', $params);
		}

		if ($this->get('params') instanceof Registry)
		{
			$this->set('params', (string) $params->toString());
		}

		return parent::save();
	}
}
