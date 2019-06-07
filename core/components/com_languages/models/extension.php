<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Filesystem;
use Notify;
use Lang;
use User;
use Date;

/**
 * Extension model
 */
class Extension extends Relational
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
	 * Language path
	 *
	 * @var  object
	 */
	protected $path = null;

	/**
	 * Language folders
	 *
	 * @var  object
	 */
	protected $folders = null;

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
		return parent::all()->whereEquals('type', 'language');
	}

	/**
	 * Get extension entry for the component
	 *
	 * @return  object
	 */
	public static function component()
	{
		return parent::all()->whereEquals('element', 'com_languages')->row();
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
	 * Get language path
	 *
	 * @return  string
	 */
	public function path($client_id = 0)
	{
		if (is_null($this->path))
		{
			$client = \Hubzero\Base\ClientManager::client($client_id);
			$client->path = '/bootstrap/' . ucfirst($client->name);

			//$client = $this->client($client_id);
			$this->path = (string)Lang::getLanguagePath($client->path);
		}

		return $this->path;
	}

	/**
	 * Method to get the folders
	 *
	 * @param   integer  $client_id
	 * @return  array    Languages folders
	 */
	public function folders($client_id = 0)
	{
		if (is_null($this->folders))
		{
			$this->folders = Filesystem::directories(
				$this->path($client_id),
				'.',
				false,
				false,
				array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'pdf_fonts', 'overrides')
			);
			if (!$this->folders || !is_array($this->folders))
			{
				$this->folders = array();
			}
		}

		return $this->folders;
	}

	/**
	 * Method to get Languages item data
	 *
	 * @return	array
	 */
	public function info()
	{
		if (is_null($this->data))
		{
			$client_id = $this->get('client_id');
			$lang = $this->get('element');

			// Get information
			$path = $this->path($client_id);

			$dir = '/' . $lang . '/' . $lang . '.xml';
			foreach (array(PATH_APP, PATH_CORE) as $base)
			{
				$file = $base . $path . $dir;

				if (file_exists($file))
				{
					break;
				}

				$file = $base . strtolower($path) . $dir;

				if (file_exists($file))
				{
					break;
				}
			}

			$info = self::metaFile($file);

			$row = new \Hubzero\Base\Obj();
			$row->language = $lang;

			if (!is_array($info))
			{
				$row->missing = true;

				$info = array(
					'language'     => $lang,
					'name'         => $lang,
					'description'  => '',
					'version'      => '',
					'creationDate' => '',
					'author'       => '',
					'authorEmail'  => '',
					'authorUrl'    => '',
					'missing'      => true
				);
			}

			foreach ($info as $key => $value)
			{
				$row->$key = $value;
			}

			$row->checked_out = 0;

			$this->data = $row;
		}

		return $this->data;
	}

	/**
	 * Method to get Languages item data
	 *
	 * @param   string  $file
	 * @return	mixee   boolean or array
	 */
	public static function metaFile($file)
	{
		// Disable libxml errors and allow to fetch error information as needed
		libxml_use_internal_errors(true);

		// Try to load the XML file
		$xml = simplexml_load_file($file);

		if (empty($xml))
		{
			// There was an error
			Notify::warning(Lang::txt('JLIB_UTIL_ERROR_XML_LOAD') . ' ' . $file);

			foreach (libxml_get_errors() as $error)
			{
				Notify::warning('XML: ' . $error->message);
			}

			return false;
		}

		// Check for a valid XML root tag.
		// Should be 'langMetaData'.
		if ($xml->getName() != 'metafile')
		{
			unset($xml);
			return false;
		}

		$data = array();

		$data['name'] = (string) $xml->name;
		$data['type'] = $xml->attributes()->type;

		$data['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : Lang::txt('JLIB_UNKNOWN');
		$data['author']       = ((string) $xml->author) ? (string) $xml->author : Lang::txt('JLIB_UNKNOWN');

		$data['copyright']   = (string) $xml->copyright;
		$data['authorEmail'] = (string) $xml->authorEmail;
		$data['authorUrl']   = (string) $xml->authorUrl;
		$data['version']     = (string) $xml->version;
		$data['description'] = (string) $xml->description;
		$data['group']       = (string) $xml->group;

		return $data;
	}
}
