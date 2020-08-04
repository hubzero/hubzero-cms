<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Asset;

/**
 * Asset class for Javascript
 */
class Javascript extends File
{
	/**
	 * Asset type
	 *
	 * @var  string
	 */
	protected $type = 'js';

	/**
	 * Constructor
	 *
	 * @param   string  $extension  CMS Extension to load asset from
	 * @param   string  $name       Asset name (optional)
	 * @return  void
	 */
	public function __construct($extension, $name=null)
	{
		parent::__construct($extension, $name);

		// Try to detect if the asset is a declaration
		if (!$extension || strstr($name, '(') || strstr($name, ';'))
		{
			$this->declaration = true;

			// Reset the name in case any parsing/modification
			// happened in the parent constructor.
			$this->name = $name;
		}
	}
}
