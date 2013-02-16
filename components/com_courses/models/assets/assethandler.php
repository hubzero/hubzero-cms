<?php

/**
* Asset upload handler
*/
class AssetHandler
{
	// Array to hold our handler objects
	private $handlers = array();

	protected $db;

	protected $asset = array(
			'title'      => '',
			'url'        => '',
			'type'       => '',
			'created'    => '',
			'created_by' => '',
			'state'      => 0, // upublished
			'course_id'  => ''
		);

	protected $assoc = array(
			'asset_id' => '',
			'scope'    => '',
			'scope_id' => '',
		);

	public function __construct(&$db, $fileType)
	{
		// Set the database object
		$this->db = $db;

		// Initialize the object
		$this->initialize($fileType);
	}

	private function initialize($fileType)
	{
		// Grab all of the asset handlers for this file type
		$this->getHandlersThatRespondToType($fileType);
	}

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
			if (is_file($path) && strpos($path, '.php') !== false)
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
			if($class != get_class())
			{
				// Check to see if this handler responds to our current extension
				if(method_exists($class, 'getExtensions'))
				{
					$extensions = $class::getExtensions();

					if(in_array($fileType, $extensions))
					{
						$this->addHandler($class);
					}
				}
			}
		}
	}

	private function addHandler($class)
	{
		$this->handlers[] = $class;
	}

	public function getHandlers()
	{
		$handlers = array();

		foreach ($this->handlers as $h) {
			$handlers[] = array('classname'=>$h, 'message'=>$h::getMessage());
		}
		return $handlers;
	}

	public function create($class=null)
	{
		// @FIXME: how do we want to handle having an unknown extension?  Just upload it or throw an error?
		if(!empty($class) && in_array($class, $this->handlers))
		{
			$handler = $class;
		}
		elseif(isset($this->handlers[0]))
		{
			$handler = $this->handlers[0];
		}
		else
		{
			return array('error'=>'There is no option available to handle this filetype/content');
		}

		if(isset($handler) && method_exists($handler, 'create'))
		{
			return $handler::create();
		}
		else
		{
			return array('error'=>'This filetype/content does not have a create method');
		}
	}
}