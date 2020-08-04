<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document;

use Hubzero\Base\Obj;

/**
 * Abstract document renderer
 *
 * Inspired by Joomla's JDocumentRenderer class
 */
class Renderer extends Obj
{
	/**
	 * Reference to the Document object that instantiated the renderer
	 *
	 * @var  object
	 */
	protected $doc = null;

	/**
	 * Renderer mime type
	 *
	 * @var  string
	 */
	protected $mime = 'text/html';

	/**
	 * Class constructor
	 *
	 * @param   object  &$doc  A reference to the Document object that instantiated the renderer
	 * @return  void
	 */
	public function __construct(&$doc)
	{
		$this->doc = &$doc;
	}

	/**
	 * Renders a script and returns the results as a string
	 *
	 * @param   string  $name     The name of the element to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 * @return  string  The output of the script
	 */
	public function render($name, $params = null, $content = null)
	{
		// ...
	}

	/**
	 * Return the content type of the renderer
	 *
	 * @return  string  The contentType
	 */
	public function getContentType()
	{
		return $this->mime;
	}
}
