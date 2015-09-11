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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import;

// needed files
require_once __DIR__ . DS . 'interfaces' . DS . 'adapter.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'import' . DS . 'archive.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'import' . DS . 'record.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'import' . DS . 'hook' . DS . 'archive.php';

/**
 * Import Importer class
 */
class Importer
{
	/**
	 * ResourceImportInterface Object
	 *
	 * @access private
	 */
	private $adapter;

	/**
	 * Array of Resource Import Adapters
	 *
	 * @access private
	 */
	private $adapters;

	/**
	 * Resource Import Repository Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// get adapaters
		$this->bootAdapters();
	}

	/**
	 * Get Instance of importer
	 *
	 * @param   $key   Instance Key
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Method to boot import adapaters
	 *
	 * @access private
	 * @return void
	 */
	private function bootAdapters()
	{
		// include all adapters
		foreach (glob(__DIR__ . DS . 'adapters' . DS . '*.php') as $adapter)
		{
			require_once $adapter;
		}

		// anonymous function to get adapters
		$isAdapterClass = function($class) {

			return (in_array('Components\Resources\Import\Interfaces\Adapter', class_implements($class)));
		};

		// set our adapters (any declared class implementing the ResourcesImportInterface)
		$this->adapters = array_values(array_filter(get_declared_classes(), $isAdapterClass));
	}

	/**
	 * Method to set adapater
	 *
	 * @access public
	 * @param  ResourcesImportInterface Object
	 * @return void
	 */
	public function setAdapter(\Components\Resources\Import\Interfaces\Adapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * Method to get adapater
	 *
	 * @access public
	 * @return ResourcesImportInterface Object
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Method auto detect adapter based on mime type
	 *
	 * @access private
	 * @param  ResourcesImportInterface Object
	 * @return void
	 */
	private function autoDetectAdapter(\Components\Resources\Models\Import $import)
	{
		// make sure we dont already have an adapter
		if ($this->adapter)
		{
			return;
		}

		// get path to data file
		$dataPath = $import->getDataPath();

		// get the mime type of file
		$file  = finfo_open(FILEINFO_MIME_TYPE);
		$mime  = finfo_file($file, $dataPath);

		// anonymous function to see if we can use any
		$respondsTo = function($class) use ($mime) {
			return $class::accepts($mime);
		};

		// set the adapter if we found one
		$responded = array_filter($this->adapters, $respondsTo);
		if ($adapter = array_shift($responded))
		{
			$this->setAdapter(new $adapter());
		}

		// do we still not have adapter
		if (!$this->adapter)
		{
			throw new \Exception(Lang::txt('Resource Import: No adapter found to count import data.'));
		}
	}

	/**
	 * Count import data
	 *
	 * @access public
	 * @return void
	 */
	public function count(\Components\Resources\Models\Import $import)
	{
		// autodetect adapter
		$this->autoDetectAdapter($import);

		// call count on adapter
		return $this->adapter->count($import);
	}

	/**
	 * Process import data
	 *
	 * @access public
	 * @param  Closure Object
	 * @return void
	 */
	public function process(\Components\Resources\Models\Import $import, array $callbacks, $dryRun = 1)
	{
		// autodetect adapter
		$this->autoDetectAdapter($import);

		// mark import run
		$import->markRun($dryRun);

		// call process on adapter
		return $this->adapter->process($import, $callbacks, $dryRun);
	}
}