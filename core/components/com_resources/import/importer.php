<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import;

// needed files
require_once __DIR__ . DS . 'interfaces' . DS . 'adapter.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'import' . DS . 'run.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'import' . DS . 'record.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'import' . DS . 'hook.php';

/**
 * Import Importer class
 */
class Importer
{
	/**
	 * ResourceImportInterface Object
	 *
	 * @var  object
	 */
	private $adapter;

	/**
	 * Array of Resource Import Adapters
	 *
	 * @var  array
	 */
	private $adapters;

	/**
	 * Resource Import Repository Constructor
	 *
	 * @return  void
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
	 * @return  void
	 */
	private function bootAdapters()
	{
		// include all adapters
		foreach (glob(__DIR__ . DS . 'adapters' . DS . '*.php') as $adapter)
		{
			require_once $adapter;
		}

		// anonymous function to get adapters
		$isAdapterClass = function($class)
		{
			return (in_array('Components\Resources\Import\Interfaces\Adapter', class_implements($class)));
		};

		// set our adapters (any declared class implementing the ResourcesImportInterface)
		$this->adapters = array_values(array_filter(get_declared_classes(), $isAdapterClass));
	}

	/**
	 * Method to set adapater
	 *
	 * @param   object  $adapter
	 * @return  void
	 */
	public function setAdapter(\Components\Resources\Import\Interfaces\Adapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * Method to get adapater
	 *
	 * @return  object  ResourcesImportInterface
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Method auto detect adapter based on mime type
	 *
	 * @param   object  $import
	 * @return  void
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
		$respondsTo = function($class) use ($mime)
		{
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
	 * @param   object  $import
	 * @return  void
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
	 * @param   object   $import
	 * @param   array    $callbacks
	 * @param   integer  $dryRun
	 * @return  void
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
