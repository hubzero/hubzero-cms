<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Models;

use Hubzero\Database\Relational;
use Filesystem;
use Lang;

/**
 * Extension model
 */
class Extension extends Relational
{
	/**
	 * The table primary key name
	 *
	 * @var  string
	 */
	protected $pk = 'extension_id';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'extension_id';

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
		'name' => 'notempty'
	);

	/**
	 * Delete the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		if ($this->get('protected'))
		{
			$this->addError($this->get('name') . ' is a protected extension');
			return false;
		}

		return parent::destroy();
	}

	/**
	 * Translate the entry
	 *
	 * @return  void
	 */
	public function translate()
	{
		$lang = Lang::getRoot();

		if (strlen($this->get('manifest_cache')))
		{
			$data = json_decode($this->get('manifest_cache'));
			if ($data)
			{
				foreach ($data as $key => $value)
				{
					if ($key == 'type')
					{
						// ignore the type field
						continue;
					}
					$this->set($key, $value);
				}
			}
		}
		$this->set('author_info', $this->get('authorEmail') . '<br />' . $this->get('authorUrl'));
		$this->set('client', $this->get('client_id') ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE'));

		$path = PATH_APP;

		switch ($this->get('type'))
		{
			case 'component':
				$extension = $this->get('element');
				$source = PATH_APP . '/components/' . $extension . '/admin';

				$lang->load("$extension.sys", PATH_APP, null, false, true) ||
				$lang->load("$extension.sys", $source, null, false, true);
			break;
			case 'file':
				$extension = 'files_' . $this->get('element');
				$lang->load("$extension.sys", PATH_APP, null, false, true);
			break;
			case 'library':
				$extension = 'lib_' . $this->get('element');
				$lang->load("$extension.sys", PATH_APP, null, false, true) ||
				$lang->load("$extension.sys", PATH_CORE, null, false, true);
			break;
			case 'module':
				$extension = $this->get('element');
				$source = $path . '/modules/' . $extension;
					$lang->load("$extension.sys", $path, null, false, true)
				||	$lang->load("$extension.sys", $source, null, false, true);
			break;
			case 'package':
				$extension = $this->get('element');
				$lang->load("$extension.sys", PATH_APP, null, false, true) ||
				$lang->load("$extension.sys", PATH_CORE, null, false, true);
			break;
			case 'plugin':
				$extension = 'plg_' . $this->get('folder') . '_' . $this->get('element');
				$source = PATH_CORE . '/plugins/' . $this->get('folder') . '/' . $this->get('element');

				$lang->load("$extension.sys", PATH_APP, null, false, true) ||
				$lang->load("$extension.sys", $source, null, false, true);
			break;
			case 'template':
				$extension = 'tpl_' . $this->get('element');
				$source = $path . '/templates/' . $this->get('element');

				$lang->load("$extension.sys", $path, null, false, true) ||
				$lang->load("$extension.sys", $source, null, false, true);
			break;
		}

		if (!in_array($this->get('type'), array('language', 'template', 'library')))
		{
			$this->set('name', Lang::txt($this->get('name')));
		}

		//settype($this->description, 'string');

		if (!in_array($this->get('type'), array('language')))
		{
			$this->set('description', Lang::txt($this->get('description')));
		}
	}

	/**
	 * Publsh an entry
	 *
	 * @return  bool
	 */
	public function publish()
	{
		$this->set('enabled', self::STATE_PUBLISHED);

		return $this->save();
	}

	/**
	 * Unpublish an entry
	 *
	 * @return  bool
	 */
	public function unpublish()
	{
		if ($this->get('type') == 'template')
		{
			if (is_file(\Component::path('com_templates') . '/models/style.php'))
			{
				include_once \Component::path('com_templates') . '/models/style.php';

				$style = \Components\Templates\Models\Style::all()
					->whereEquals('template', $this->get('element'))
					->whereEquals('client_id', $this->get('client_id'))
					->whereEquals('home', 1)
					->row();

				if ($style && $style->get('id'))
				{
					$this->addError(Lang::txt('COM_INSTALLER_ERROR_DISABLE_DEFAULT_TEMPLATE_NOT_PERMITTED'));
					return false;
				}
			}
		}

		$this->set('enabled', self::STATE_UNPUBLISHED);

		return $this->save();
	}

	/**
	 * Refreshes the extension table cache
	 *
	 * @return  boolean  Result of operation, true if updated, false on failure
	 */
	public function refreshManifestCache()
	{
		// Need to find to find where the XML file is since we don't store this normally
		switch ($this->get('type'))
		{
			case 'component':
				$path = DS . 'components' . DS . $this->get('element') . DS . $this->get('element') . '.xml';
			break;

			case 'plugin':
				$path = DS . 'plugins' . DS . $this->get('folder') . DS . $this->get('element') . DS . $this->get('element') . '.xml';
			break;

			case 'module':
				$path = DS . 'modules' . DS . $this->get('element') . DS . $this->get('element') . '.xml';
			break;

			case 'template':
				$path = DS . 'templates' . DS . $this->get('element') . DS . 'templateDetails.xml';
			break;

			case 'package':
				$path = DS . 'manifests' . DS . 'packages' . DS . $this->get('element') . '.xml';
			break;

			case 'library':
				$path = DS . 'manifests' . DS . 'libraries' . DS . $this->get('element') . '.xml';
			break;

			case 'language':
				$client = \Hubzero\Base\ClientManager::client($this->get('client_id'));
				$path = DS . 'bootstrap' . DS . $client->name . DS . 'language' . DS . $this->get('element') . DS . $this->get('element') . '.xml';
			break;
		}

		$paths = array(
			'app'  => Filesystem::cleanPath(PATH_APP . $path),
			'core' => Filesystem::cleanPath(PATH_CORE . $path)
		);

		$xml = null;

		foreach ($paths as $p)
		{
			if (file_exists($p))
			{
				// Disable libxml errors and allow to fetch error information as needed
				libxml_use_internal_errors(true);

				$xml = simplexml_load_file($p);
				break;
			}
		}

		if (!$xml)
		{
			return true;
		}

		if ($xml->getName() != 'install'
		 && $xml->getName() != 'extension'
		 && $xml->getName() != 'metafile')
		{
			return true;
		}

		$data = array();
		$data['legacy'] = ($xml->getName() == 'mosinstall' || $xml->getName() == 'install');
		$data['name'] = (string) $xml->name;

		// Check if we're a language. If so use metafile.
		$data['type'] = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->type;

		$data['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : Lang::txt('Unknown');
		$data['author'] = ((string) $xml->author) ? (string) $xml->author : Lang::txt('Unknown');
		$data['copyright'] = (string) $xml->copyright;
		$data['authorEmail'] = (string) $xml->authorEmail;
		$data['authorUrl'] = (string) $xml->authorUrl;
		$data['version'] = (string) $xml->version;
		$data['description'] = (string) $xml->description;
		$data['group'] = (string) $xml->group;

		$manifest = json_encode($data);

		$this->set('manifest_cache', $manifest);
		$this->set('name', $data['name']);

		if (!$this->save())
		{
			return false;
		}

		return true;
	}
}
