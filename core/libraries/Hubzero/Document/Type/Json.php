<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;

/**
 * JSON document class for parsing and displaying JSON data
 *
 * Inspired by Joomla's JDocumentJson class
 */
class Json extends Base
{
	/**
	 * Document name
	 *
	 * @var  string
	 */
	protected $name = 'hubzero';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Set mime type
		$this->mime = 'application/json';

		// Set document type
		$this->type = 'json';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  object   The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		\App::get('response')->headers->set('Cache-Control', 'no-cache', false);
		\App::get('response')->headers->set('Pragma', 'no-cache');
		\App::get('response')->headers->set('Content-disposition', 'attachment; filename="' . $this->getName() . '.json"', true);

		parent::render();

		return $this->getBuffer();
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 * @return  object  instance of $this to allow chaining
	 */
	public function setName($name)
	{
		$this->name = (string) $name;

		return $this;
	}
}
