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
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\Doc;

use ReflectionClass;
use phpDocumentor\Reflection\DocBlock;

/**
 * Documentation Generator Class
 */
class Generator
{
	/**
	 * Cache results?
	 * 
	 * @var  bool
	 */
	private $cache = true;

	/**
	 * Var to hold sections
	 * 
	 * @var  array
	 */
	private $sections = array();

	/**
	 * Var to hold output
	 * 
	 * @var  array
	 */
	private $output = array();

	/**
	 * Create sections from component api controllers
	 *
	 * @param   bool  $cache  Cache results?
	 * @return  void
	 */
	public function __construct($cache = true)
	{
		// create all needed keys in output
		$this->output = array(
			'sections' => array(),
			'versions' => array(
				'max'       => '',
				'available' => array()
			),
			'errors'   => array(),
			'files'    => array()
		);
	}

	/**
	 * Return documentation
	 * 
	 * @param   string  $format  Output format
	 * @param   bool    $format  Force new version
	 * @return  string
	 */
	public function output($format = 'json', $force = false)
	{
		// generate output
		if ($force || !$this->cache())
		{
			$this->generate();
		}

		// option to switch formats
		switch ($format)
		{
			case 'array':
				break;
			case 'php':
				$this->output = serialize($this->output);
				break;
			case 'json':
			default:
				$this->output = json_encode($this->output);
		}

		return $this->output;
	}

	/**
	 * Load from cache
	 * 
	 * @return  boolean
	 */
	private function cache()
	{
		if (!$this->cache)
		{
			return false;
		}

		// get developer params to get cache expiration
		$developerParams = \App::get('component')->params('com_developer');
		$cacheExpiration = $developerParams->get('doc_expiration', 720);

		// cache file
		$cacheFile = PATH_APP . DS . 'cache' . DS . 'api' . DS . 'documentation.json';

		// check if we have a cache file 
		if (file_exists($cacheFile))
		{
			// check if its still valid
			$cacheMakeTime = @filemtime($cacheFile);
			if (time() - $cacheExpiration < $cacheMakeTime)
			{
				$this->output = json_decode(file_get_contents($cacheFile), true);
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate Doc
	 * 
	 * @return  void
	 */
	private function generate()
	{
		// only load sections if we dont have a cache
		$this->discoverComponentSections();

		// generate output by processing sections
		$this->output['sections'] = $this->processComponentSections($this->sections);

		// remove duplicate available versions & order
		$this->output['versions']['available'] = array_unique($this->output['versions']['available']);

		// get the highest version available
		$this->output['versions']['max'] = end($this->output['versions']['available']);

		// create cache folder
		$cacheFile = PATH_APP . DS . 'cache' . DS . 'api' . DS . 'documentation.json';
		if (!\App::get('filesystem')->exists(dirname($cacheFile)))
		{
			\App::get('filesystem')->makeDirectory(dirname($cacheFile));
		}

		// save cache file
		file_put_contents($cacheFile, json_encode($this->output));
	}

	/**
	 * Load api controller files and group by component
	 * 
	 * @return  void
	 */
	private function discoverComponentSections()
	{
		// group by component
		foreach (glob(PATH_CORE . DS . 'components' . DS . 'com_*' . DS . 'api') as $path)
		{
			// get component
			$pieces = explode(DS, $path);
			array_pop($pieces);
			$component = str_replace('com_', '', array_pop($pieces));

			// add all matching files to section
			$this->sections[$component] = glob($path . DS . 'controllers' . DS . '*.php');
		}
	}

	/** 
	 * Process sections
	 * 
	 * @param   array  $sections  All the component api controllers grouped by component
	 * @return  array
	 */
	private function processComponentSections($sections)
	{
		// var to hold output
		$output = array();

		// loop through each component grouping
		foreach ($sections as $component => $files)
		{
			// if we dont have an array for that component lets create it
			if (!isset($output[$component]))
			{
				$output[$component] = [];
			}

			// loop through each file
			foreach ($files as $file)
			{
				$output[$component] = array_merge($output[$component], $this->processFile($file));
			}
		}

		// return output
		return $output;
	}

	/**
	 * Process an individual file
	 * 
	 * @param   string  $file  File path
	 * @return  array   Processed endpoints
	 */
	private function processFile($file)
	{
		// var to hold output
		$output = array();

		require_once $file;

		$className = $this->parseClassFromFile($file);
		$component = $this->parseClassFromFile($file, true)['component'];
		$version   = $this->parseClassFromFile($file, true)['version'];

		// Push file to files array
		$this->output['files'][] = $file;

		// Push version to versions array
		$this->output['versions']['available'][] = $version;

		if (!class_exists($className))
		{
			return $output;
		}

		$classReflector = new ReflectionClass($className);

		foreach ($classReflector->getMethods() as $method)
		{
			// Create docblock object & make sure we have something
			$phpdoc = new DocBlock($method);

			// Skip methods we don't want processed
			if (substr($method->getName(), -4) != 'Task' || in_array($method->getName(), array('registerTask', 'unregisterTask', 'indexTask')))
			{
				continue;
			}

			// Skip method in the parent class (already processed), 
			if ($className != $method->getDeclaringClass()->getName())
			{
				//continue;
			}

			// Skip if we dont have a short desc
			// but put in error
			if (!$phpdoc->getShortDescription())
			{
				$this->output['errors'][] = sprintf('Missing docblock for method "%s" in "%s"', $method->getName(), $file);
				continue;
			}

			// Create endpoint data array
			$endpoint = array(
				//'name'        => substr($method->getName(), 0, -4),
				//'description' => preg_replace('/\s+/', ' ', $phpdoc->getShortDescription()), // $phpdoc->getLongDescription()->getContents()
				'name'        => $phpdoc->getShortDescription(),
				'description' => $phpdoc->getLongDescription()->getContents(),
				'method'      => '',
				'uri'         => '',
				'parameters'  => array(),
				'_metadata'   => array(
					'component' => $component,
					'version'   => $version,
					'method'    => $method->getName()
				)
			);

			// Loop through each tag
			foreach ($phpdoc->getTags() as $tag)
			{
				$name    = strtolower(str_replace('api', '', $tag->getName()));
				$content = $tag->getContent();

				// Handle parameters separately
				// json decode param input
				if ($name == 'parameter')
				{
					$parameter = json_decode($content);

					if (json_last_error() != JSON_ERROR_NONE)
					{
						$this->output['errors'][] = sprintf('Unable to parse parameter info for method "%s" in "%s"', $method->getName(), $file);
						continue;
					}

					$endpoint['parameters'][] = (array) $parameter;
					continue;
				}

				if ($name == 'uri' && $method->getName() == 'indexTask')
				{
					$content .= $component;
				}

				// Add data to endpoint data
				$endpoint[$name] = $content;
			}

			// Add endpoint to output
			// We always want indexTask to be first in the list
			if ($method->getName() == 'indexTask')
			{
				array_unshift($output, $endpoint);
			}
			else
			{
				$output[] = $endpoint;
			}
		}

		return $output;
	}

	/**
	 * Get class name based on file
	 * 
	 * @param   string  $file           File path
	 * @param   bool    $returnAsParts  Return as parts?
	 * @return  mixed
	 */
	private function parseClassFromFile($file, $returnAsParts = false)
	{
		// replace some values in file path to get what we need
		$file = str_replace(
			array(
				PATH_CORE . DS . 'components' . DS . 'com_',
				PATH_APP . DS . 'components' . DS . 'com_',
				'.php'
			),
			array('', '', ''),
			$file
		);

		// split by "/"
		$parts = explode(DS, $file);
		array_unshift($parts, 'components');

		// do we want to return as parts?
		if ($returnAsParts)
		{
			$parts['namespace']  = $parts[0];
			$parts['component']  = $parts[1];
			$parts['client']     = $parts[2];
			$parts['controller'] = $parts[3];
			$b = explode('v', $parts[3]);
			$parts['version']    = end($b);//$parts[4];
			return $parts;
		}

		// capitalize first letter
		$parts = array_map('ucfirst', $parts);

		// put all the pieces back together
		return str_replace('.', '_', implode('\\', $parts));
	}
}