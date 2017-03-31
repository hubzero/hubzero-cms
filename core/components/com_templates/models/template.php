<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Templates\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Base\Object;
use Filesystem;
use Lang;

include_once __DIR__ . DS . 'file.php';
include_once __DIR__ . DS . 'style.php';

/**
 * Template style model
 */
class Template extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__extensions';

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
	 * @var string
	 */
	public $orderBy = 'id';

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
		'name'    => 'notempty',
		'element' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'element'
	);

	/**
	 * Config registry for params
	 *
	 * @var  object
	 */
	protected $registry = null;

	/**
	 * Xml data
	 *
	 * @var  object
	 */
	protected $xmldata = null;

	/**
	 * Runs extra setup code when creating a new model
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('name', function($data)
		{
			$any = self::all()
				->whereEquals('name', $data['name'])
				->where('extension_id', '!=', $data['extension_id'])
				->total();

			if ($any)
			{
				return Lang::txt('Name already exists.');
			}

			return false;
		});
	}

	/**
	 * Generates automatic element field value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticElement($data)
	{
		return preg_replace("/[^A-Z0-9_\.-]/i", '', trim($data['element']));
	}

	/**
	 * Get params as a Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!$this->registry)
		{
			$this->registry = new Registry($this->get('params'));
		}

		return $this->registry;
	}

	/**
	 * Get params as a Registry object
	 *
	 * @return  object
	 */
	public function transformXml()
	{
		if (!$this->xmldata)
		{
			$filePath = Filesystem::cleanPath(($this->get('protected') ? PATH_CORE : PATH_APP) . '/templates/' . $this->get('element') . '/templateDetails.xml');

			$this->xmldata = new Object;

			// Check of the xml file exists
			if (is_file($filePath))
			{
				// Disable libxml errors and allow to fetch error information as needed
				libxml_use_internal_errors(true);

				$xml = simplexml_load_file($filePath);

				if ($xml)
				{
					// Check for a valid XML root tag.
					//
					// Should be 'install', but for backward compatibility we will accept 'extension'.
					// Languages use 'metafile' instead
					if ($xml->getName() != 'install'
					 && $xml->getName() != 'extension'
					 && $xml->getName() != 'metafile')
					{
						return $this->xmldata;
					}

					$meta = array();
					$meta['legacy']       = ($xml->getName() == 'mosinstall' || $xml->getName() == 'install');
					$meta['name']         = (string) $xml->name;

					// Check if we're a language. If so use metafile.
					$meta['type']         = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->type;
					$meta['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : Lang::txt('Unknown');
					$meta['author']       = ((string) $xml->author) ? (string) $xml->author : Lang::txt('Unknown');
					$meta['copyright']    = (string) $xml->copyright;
					$meta['authorEmail']  = (string) $xml->authorEmail;
					$meta['authorUrl']    = (string) $xml->authorUrl;
					$meta['version']      = (string) $xml->version;
					$meta['description']  = (string) $xml->description;
					$meta['group']        = (string) $xml->group;

					if ($meta['type'] != 'template')
					{
						return $this->xmldata;
					}

					foreach ($meta as $key => $value)
					{
						$this->xmldata->set($key, $value);
					}
				}
			}
		}

		return $this->xmldata;
	}

	/**
	 * Generates a list of template styles
	 *
	 * @return  object
	 */
	public function styles()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Style', 'template', 'element');
	}

	/**
	 * Method to get a list of all the files to edit in a template.
	 *
	 * @return  array  A nested array of relevant files.
	 */
	public function files()
	{
		// Initialise variables.
		$result = array();

		if ($this->get('extension_id'))
		{
			//$client = \Hubzero\Base\ClientManager::client($this->get('client_id'));
			$base = ($this->get('protected') ? PATH_CORE : PATH_APP) . '/templates/' . $this->get('element');
			$path = Filesystem::cleanPath($base . '/');

			// Check if the template path exists.
			if (is_dir($path))
			{
				$result['main'] = array();
				$result['clo']  = array();
				$result['html'] = array();

				$main = array(
					'index.php',
					'error.php',
					'print.php',
					'component.php',
					'offline.php',
					'group.php',
					'email.php'
				);

				$files = Filesystem::files($path, '\.css|\.php|\.less|\.js|\.scss$', true, true);

				foreach ($files as $file)
				{
					$file = str_replace($path, '', $file);

					if (in_array($file, $main))
					{
						$key = str_replace('.php', '', $file);

						$result['main'][$key] = new File($file, $this->get('extension_id'));
						continue;
					}

					if (substr($file, 0, strlen('html/')) == 'html/')
					{
						$result['html'][] = new File($file, $this->get('extension_id'));
					}
					else
					{
						$result['clo'][] = new File($file, $this->get('extension_id'));
					}
				}
			}
			else
			{
				$this->addError(Lang::txt('COM_TEMPLATES_ERROR_TEMPLATE_FOLDER_NOT_FOUND'));
			}
		}

		return $result;
	}
}
