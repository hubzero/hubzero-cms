<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * Var to hold sections
	 * 
	 * @var  array
	 */
	private $sections = [];

	/**
	 * Var to hold output
	 * 
	 * @var  array
	 */
	private $output = [];

	/**
	 * Create sections from component api controllers
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// create all needed keys in output
		$this->output = [
			'sections' => [],
			'versions' => ['max' => '', 'available' => []],
			'errors'   => [],
			'files'    => []
		];
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
	 * @return  mixed  Documentation
	 */
	private function cache()
	{
		return false;
		// get developer params to get cache expiration
		$developerParams = \App::get('component')->params('com_developer');
		$cacheExpiration = $developerParams->get('doc_expiration', 720);

		// cache file
		$cacheFile = PATH_APP . DS . 'app' . DS . 'cache' . DS . 'api' . DS . 'documentation.json';

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
	 * @return  mixed  Documentation
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
	 * @param  string $file File path
	 * @return array        Processed endpoints
	 */
	private function processFile($file)
	{
		// var to hold output
		$output = array();

		// include file
		require_once $file;

		// get file class name
		$className = $this->parseClassFromFile($file);

		// component
		$component = $this->parseClassFromFile($file, true)['component'];

		// get file version
		$version = $this->parseClassFromFile($file, true)['version'];

		// push file to files array
		$this->output['files'][] = $file;

		// push version to versions array
		$this->output['versions']['available'][] = $version;

		if (!class_exists($className))
		{
			return $output;
		}

		// create reflection class of file
		$classReflector = new ReflectionClass($className);

		// loop through each method and process doc
		foreach ($classReflector->getMethods() as $method)
		{
			// create docblock object & make sure we have something
			$phpdoc = new DocBlock($method);

			// skip constructor
			if ($method->getName() == '__construct')
			{
				continue;
			}

			// skip  method in the parent class (already processed), 
			if ($className != $method->getDeclaringClass()->getName())
			{
				//continue;
			}

			// skip if we dont have a short desc
			// but put in error
			if (!$phpdoc->getShortDescription())
			{
				$this->output['errors'][] = sprintf('Missing docblock for method "%s" in "%s"', $method->getName(), $file);
				continue;
			}

			// create endpoint data array
			$endpoint = array(
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

			// loop through each tag
			foreach ($phpdoc->getTags() as $tag)
			{
				// get tag name and content
				$name    = strtolower(str_replace('api', '', $tag->getName()));
				$content = $tag->getContent();

				// handle parameters separately
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

				// add data to endpoint data
				$endpoint[$name] = $content;
			}

			// add endpoint to output
			$output[] = $endpoint;
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
			array(PATH_CORE . DS . 'components' . DS . 'com_', '.php'),
			array('', ''),
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