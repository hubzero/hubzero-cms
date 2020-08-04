<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form;

/**
 * Provides a storage for filesystem's paths where Form's entities reside and methods for creating those entities.
 * Also stores objects with entities' prototypes for further reusing.
 *
 * Inspired by Joomla's JFormHelper class
 *
 * @todo  Rewrite all of this.
 */
class Helper
{
	/**
	 * Array with paths where entities(field, rule, form) can be found.
	 *
	 * Array's structure:
	 * <code>
	 * paths:
	 * {ENTITY_NAME}:
	 * - /path/1
	 * - /path/2
	 * </code>
	 *
	 * @var  array
	 */
	protected static $paths;

	/**
	 * Static array of Form's entity objects for re-use.
	 * Prototypes for all fields and rules are here.
	 *
	 * Array's structure:
	 * <code>
	 * entities:
	 * {ENTITY_NAME}:
	 * {KEY}: {OBJECT}
	 * </code>
	 *
	 * @var  array
	 */
	protected static $entities = array();

	/**
	 * Method to load a form field object given a type.
	 *
	 * @param   string   $type  The field type.
	 * @param   boolean  $new   Flag to toggle whether we should get a new instance of the object.
	 * @return  mixed    Field object on success, false otherwise.
	 */
	public static function loadFieldType($type, $new = true)
	{
		return self::loadType('field', $type, $new);
	}

	/**
	 * Method to load a form rule object given a type.
	 *
	 * @param   string   $type  The rule type.
	 * @param   boolean  $new   Flag to toggle whether we should get a new instance of the object.
	 * @return  mixed    Rule object on success, false otherwise.
	 */
	public static function loadRuleType($type, $new = true)
	{
		return self::loadType('rule', $type, $new);
	}

	/**
	 * Method to load a form entity object given a type.
	 * Each type is loaded only once and then used as a prototype for other objects of same type.
	 * Please, use this method only with those entities which support types (forms don't support them).
	 *
	 * @param   string   $entity  The entity.
	 * @param   string   $type    The entity type.
	 * @param   boolean  $new     Flag to toggle whether we should get a new instance of the object.
	 * @return  mixed    Entity object on success, false otherwise.
	 */
	protected static function loadType($entity, $type, $new = true)
	{
		// Reference to an array with current entity's type instances
		$types = &self::$entities[$entity];

		// Initialize variables.
		$key   = md5($type);
		$class = '';

		// Return an entity object if it already exists and we don't need a new one.
		if (isset($types[$key]) && $new === false)
		{
			return $types[$key];
		}

		if (($class = self::loadClass($entity, $type)) !== false)
		{
			// Instantiate a new type object.
			$types[$key] = new $class;

			return $types[$key];
		}

		return false;
	}

	/**
	 * Attempt to import the Field class file if it isn't already imported.
	 * You can use this method outside of Form for loading a field for inheritance or composition.
	 *
	 * @param   string  $type  Type of a field whose class should be loaded.
	 * @return  mixed   Class name on success or false otherwise.
	 */
	public static function loadFieldClass($type)
	{
		return self::loadClass('field', $type);
	}

	/**
	 * Attempt to import the Rule class file if it isn't already imported.
	 * You can use this method outside of Form for loading a rule for inheritance or composition.
	 *
	 * @param   string  $type  Type of a rule whose class should be loaded.
	 * @return  mixed   Class name on success or false otherwise.
	 */
	public static function loadRuleClass($type)
	{
		return self::loadClass('rule', $type);
	}

	/**
	 * Load a class for one of the form's entities of a particular type.
	 * Currently, it makes sense to use this method for the "field" and "rule" entities
	 * (but you can support more entities in your subclass).
	 *
	 * @param   string  $entity  One of the form entities (field or rule).
	 * @param   string  $type    Type of an entity.
	 * @return  mixed   Class name on success or false otherwise.
	 */
	protected static function loadClass($entity, $type)
	{
		$parts = explode('_', $type);
		$parts = array_map('ucfirst', $parts);
		$parts = implode('\\', $parts);

		$class = __NAMESPACE__ . '\\' . ucfirst($entity) . 's' . '\\' . $parts;

		if (class_exists($class))
		{
			return $class;
		}

		// Get the field search path array.
		$paths = self::addPath($entity);

		// If the type is complex, add the base type to the paths.
		if ($pos = strpos($type, '_'))
		{

			// Add the complex type prefix to the paths.
			for ($i = 0, $n = count($paths); $i < $n; $i++)
			{
				// Derive the new path.
				$path = $paths[$i] . '/' . strtolower(substr($type, 0, $pos));

				// If the path does not exist, add it.
				if (!in_array($path, $paths))
				{
					$paths[] = $path;
				}
			}
			// Break off the end of the complex type.
			$type = substr($type, $pos + 1);
		}

		// Try to find the class file.
		$type = strtolower($type) . '.php';

		foreach ($paths as $path)
		{
			if ($file = self::find($path, $type))
			{
				require_once $file;

				if (class_exists($class))
				{
					break;
				}
			}
		}

		// Check for all if the class exists.
		return class_exists($class) ? $class : false;
	}

	/**
	 * Searches the directory paths for a given file.
	 *
	 * @param   mixed   $paths  An path string or array of path strings to search in
	 * @param   string  $file   The file name to look for.
	 * @return  mixed   Full path and name for the target file, or false if file not found.
	 */
	protected static function find($paths, $file)
	{
		$paths = is_array($paths) ? $paths : array($paths);

		foreach ($paths as $path)
		{
			$fullname = $path . DIRECTORY_SEPARATOR . $file;

			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path     = realpath($path);
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		return false;
	}

	/**
	 * Method to add a path to the list of field include paths.
	 *
	 * @param   mixed  $new  A path or array of paths to add.
	 * @return  array  The list of paths that have been added.
	 */
	public static function addFieldPath($new = null)
	{
		return self::addPath('field', $new);
	}

	/**
	 * Method to add a path to the list of form include paths.
	 *
	 * @param   mixed  $new  A path or array of paths to add.
	 * @return  array  The list of paths that have been added.
	 */
	public static function addFormPath($new = null)
	{
		return self::addPath('form', $new);
	}

	/**
	 * Method to add a path to the list of rule include paths.
	 *
	 * @param   mixed  $new  A path or array of paths to add.
	 * @return  array  The list of paths that have been added.
	 */
	public static function addRulePath($new = null)
	{
		return self::addPath('rule', $new);
	}

	/**
	 * Method to add a path to the list of include paths for one of the form's entities.
	 * Currently supported entities: field, rule and form. You are free to support your own in a subclass.
	 *
	 * @param   string  $entity  Form's entity name for which paths will be added.
	 * @param   mixed   $new     A path or array of paths to add.
	 * @return  array   The list of paths that have been added.
	 */
	protected static function addPath($entity, $new = null)
	{
		// Reference to an array with paths for current entity
		$paths = &self::$paths[$entity];

		// Add the default entity's search path if not set.
		if (empty($paths))
		{
			// While we support limited number of entities (form, field and rule)
			// we can do this simple pluralisation:
			$entity_plural = ucfirst($entity) . 's';

			$paths[] = __DIR__ . DIRECTORY_SEPARATOR . $entity_plural;
		}

		// Force the new path(s) to an array.
		settype($new, 'array');

		// Add the new paths to the stack if not already there.
		foreach ($new as $path)
		{
			if (!in_array($path, $paths))
			{
				array_unshift($paths, trim($path));
			}
		}

		return $paths;
	}
}
