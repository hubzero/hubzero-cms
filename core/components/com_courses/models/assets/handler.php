<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Components\Courses\Models\Base;
use Components\Courses\Models\Asset;

/**
 * Courses asset handler
 */
class Handler
{
	/**
	 * Array to hold our handler objects
	 *
	 * @var array
	 **/
	private $handlers = array();

	/**
	 * Database object
	 *
	 * @var object
	 **/
	protected $db;

	/**
	 * Array to hold the asset
	 *
	 * @var array
	 **/
	protected $asset = array(
		'title'        => '',
		'url'          => '',
		'type'         => '',
		'subtype'      => '',
		'created'      => '',
		'created_by'   => '',
		'state'        => 0, // upublished
		'course_id'    => '',
		'graded'       => 0,
		'grade_weight' => ''
	);

	/**
	 * Array to hold the asset association
	 *
	 * @var array
	 **/
	protected $assoc = array(
		'asset_id' => '',
		'scope'    => '',
		'scope_id' => '',
	);

	/**
	 * Constructor - sets the database object and initializes the file types
	 *
	 * @return void
	 **/
	public function __construct(&$db, $fileType=null, $scanHandlers=true)
	{
		// Set the database object
		$this->db = $db;

		if ($scanHandlers)
		{
			// Initialize the object
			$this->initialize($fileType);
		}
	}

	/**
	 * Constructs a new object without scanning for handlers
	 *
	 * @return void
	 **/
	public static function newWithoutHandlers($db)
	{
		return new static($db, null, false);
	}

	/**
	 * Runs initialization type things
	 *
	 * @return void
	 **/
	private function initialize($fileType)
	{
		// Grab all of the asset handlers for this file type
		$this->getHandlersThatRespondToType($fileType);
	}

	/**
	 * Takes the incoming file type (extension), and checks to see if we have any handlers for that file type
	 *
	 * @return void
	 **/
	private function getHandlersThatRespondToType($fileType)
	{
		// Get the current class list
		$classes = get_declared_classes();

		// Scan the current directory for other asset handlers
		// @FIXME: also include files from template override directory (override if same name, or add to the list if different name)
		foreach (scandir(dirname(__FILE__)) as $filename)
		{
			// Create the path
			$path = dirname(__FILE__) . DS . $filename;

			// Ensure the path is a file and ends in .php
			if (is_file($path) && substr($path, -4) == '.php')
			{
				// Include the file
				require_once $path;
			}
		}

		// Diff the initial class list with the new list to derive the added classes
		$diff = array_diff(get_declared_classes(), $classes);

		// Loop through the added classes
		foreach ($diff as $class)
		{
			if ($class != get_class())
			{
				// Check to see if this handler responds to our current extension
				if (method_exists($class, 'getExtensions'))
				{
					$extensions = $class::getExtensions();

					if (in_array(strtolower($fileType), $extensions))
					{
						$this->addHandler($class);
					}
				}
			}
		}
	}

	/**
	 * API for getting action message
	 *
	 * @return message
	 **/
	public static function getMessage()
	{
		return static::$info['action_message'];
	}

	/**
	 * API for getting extensions
	 *
	 * @return array of extensions
	 **/
	public static function getExtensions()
	{
		return static::$info['responds_to'];
	}

	/**
	 * Shortcut to add handlers to the class handlers variable
	 *
	 * @return void
	 **/
	public function addHandler($class)
	{
		$this->handlers[] = $class;
	}

	/**
	 * Get handlers
	 *
	 * @return array - classname for handler and message (message that user will see when choosing a handler)
	 **/
	public function getHandlers()
	{
		$handlers = array();

		foreach ($this->handlers as $h)
		{
			$handlers[] = array('classname'=>$h, 'message'=>$h::getMessage());
		}

		return $handlers;
	}

	/**
	 * Create a new asset
	 *
	 * @return array - message and status code from individual handlers
	 **/
	public function doCreate($class=null)
	{
		// @FIXME: how do we want to handle having an unknown extension?  Just upload it or throw an error?

		// If we have an incoming class, we know to just run that
		if (!empty($class) && in_array($class, $this->handlers))
		{
			$handler = $class;
		}
		elseif (isset($this->handlers[0]))
		{
			// otherwise, just run the first in the list
			// This is a fallback - not intended to be triggered unless there was only one handler for this filetype
			$handler = $this->handlers[0];
		}
		else
		{
			// No handler class provided, and non set, oops!
			return array('error'=>'There is no option available to handle this filetype/content');
		}

		// Make sure the class exists and has a create method
		if (isset($handler) && method_exists($handler, 'create'))
		{
			// Run create and return the results
			$object = $handler::newWithoutHandlers($this->db);
			return $object->create();
		}
		else
		{
			// Oops, provided class doesn't have a create method
			return array('error'=>'This filetype/content does not have a create method');
		}
	}

	/**
	 * Edit an asset
	 *
	 * @return array
	 **/
	public function doEdit($id)
	{
		// Look up asset type from id
		require_once dirname(__DIR__) . DS . 'asset.php';
		$asset = new Asset($id);

		// Classname
		$space = __NAMESPACE__ . '\\';
		$class    = $space . ucfirst($asset->get('type'));
		$classAlt = $space . ucfirst($asset->get('subtype'));

		if ($classAlt != $space && class_exists($classAlt) && method_exists($classAlt, 'edit'))
		{
			$object = $classAlt::newWithoutHandlers($this->db);
			return $object->edit($asset);
		}
		else if ($class != $space && class_exists($class) && method_exists($class, 'edit'))
		{
			$object = $class::newWithoutHandlers($this->db);
			return $object->edit($asset);
		}
		else
		{
			// Default edit page
			return array('type'=>'default');
		}
	}

	/**
	 * Preview an asset
	 *
	 * @return array
	 **/
	public function preview($id)
	{
		// Look up asset type from id
		require_once dirname(__DIR__) . DS . 'asset.php';
		$asset = new Asset($id);

		// Classname
		$class = __NAMESPACE__ . '\\' . ucfirst($asset->get('type'));

		if ($class != __NAMESPACE__ . '\\' && class_exists($class) && method_exists($class, 'preview'))
		{
			$object = $class::newWithoutHandlers($this->db);
			return $object->preview($asset);
		}
		else
		{
			// Default edit page
			return array('type'=>'default');
		}
	}
}
