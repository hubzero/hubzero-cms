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
 * @since     2.1.1
 */

namespace Components\Search\Helpers;
use ReflectionClass;
use Hubzero\Search\Searchable;

/**
 * Solr helper class
 */
class DiscoveryHelper
{
	/**
	 * Checks to see if model is searchable
	 * @param class $class  class of object being checked
	 * @return mixed name of class if it is found, false if not
	 */
	public static function isSearchable($class)
	{
		if (is_object($class) || class_exists($class))
		{
			$reflect = new ReflectionClass($class);
			if ($reflect->implementsInterface('Hubzero\Search\Searchable'))
			{
				return $reflect->getNamespaceName();
			}
		}
		return false;
	}

	/**
	 * Gets list of all components that currently exist
	 *
	 *	@return array
	 */
	public static function getCompleteComponentList()
	{
		$coreComponentPath = PATH_CORE . '/components';
		$coreComponents = scandir($coreComponentPath);
		$appComponentDir = PATH_APP . '/components';
		$appComponents = scandir($appComponentDir);

		$allComponents = array_merge($coreComponents, $appComponents);
		$allComponents = array_map(function($component){
			$prefix = 'com_';
			$startPos = strpos($component, 'com_');
			return ($startPos !== false) ? substr($component, $startPos + strlen($prefix)) : null;
		}, $allComponents);
		$allComponents = array_filter($allComponents);
		return $allComponents;
	}

	/**
	 * Gets all components that are searchable
	 * @param array $existingComponents Components that have already been discovered
	 * @return array
	 */
	public static function getSearchableComponents($existingComponents = array())
	{
		$componentList = self::getCompleteComponentList();
		$componentList = array_diff($componentList, $existingComponents);
		$searchableComponents = array();
		foreach ($componentList as $component)
		{
			if (self::getSearchableModel($component))
			{
				$searchableComponents[] = $component;
			}
		}
		return $searchableComponents;
	}

	/**
	 * Find a searchable model in the component
	 * @param string $component name of component
	 * @return mixed
	 */
	public static function getSearchableModel($component)
	{
		$modelPath = Component::path($component) . '/models/';
		$ormModelPath = $modelPath . 'orm/';
		if (!file_exists($modelPath) && !file_exists($ormModelPath))
		{
			return false;
		}
		$models = scandir($modelPath);
		$ormModels = array();
		if (file_exists($ormModelPath))
		{
			$ormModels = scandir($ormModelPath);
		}
		$models = array_filter($models, function($model){
			$suffix = '.php';
			$suffixLength = strlen($suffix);
			if (substr($model, - $suffixLength) == $suffix)
			{
				return $model;
			}
		});
		$searchableModels = array();
		$searchableFlag = false;
		foreach ($models as $model)
		{
			$baseNameSpace = '\\Components\\' . ucfirst($component) . '\\Models\\';
			$className = ucfirst(basename($model, '.php'));
			$fullClassName = $baseNameSpace . $className;
			if (in_array($model, $ormModels))
			{
				$ormClassName = $baseNameSpace . 'Orm\\' . $className;
				if (self::isSearchable($ormClassName))
				{
					return $ormClassName;
				}
			}

			if (self::isSearchable($fullClassName))
			{
				return $fullClassName;
			}
		}
		return false;
	}
}
