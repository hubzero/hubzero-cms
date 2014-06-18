<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Resources\Import;

// needed files
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'import' . DS . 'interfaces' . DS . 'adapter.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'import' . DS . 'archive.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'import' . DS . 'record.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'import' . DS . 'hook' . DS . 'archive.php';

/**
 * Import Importer class
 */
class Importer {

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
		foreach (glob(JPATH_COMPONENT_ADMINISTRATOR . DS . 'import' . DS . 'adapters' . DS . '*.php') as $adapter)
		{
			require_once $adapter;
		}

		// anonymous function to get adapters
		$isAdapterClass = function($class) {

			return (in_array('Resources\Import\Interfaces\Adapter', class_implements($class)));
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
	public function setAdapter(\Resources\Import\Interfaces\Adapter $adapter)
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
	private function autoDetectAdapter(\Resources\Model\Import $import)
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
			throw new \Exception( \JText::_('Resource Import: No adapter found to count import data.') );
		}
	}

	/**
	 * Count import data
	 *
	 * @access public
	 * @return void
	 */
	public function count(\Resources\Model\Import $import)
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
	public function process(\Resources\Model\Import $import, array $callbacks, $dryRun = 1)
	{
		// autodetect adapter
		$this->autoDetectAdapter($import);

		// mark import run
		$import->markRun($dryRun);

		// call process on adapter
		return $this->adapter->process($import, $callbacks, $dryRun);
	}
}