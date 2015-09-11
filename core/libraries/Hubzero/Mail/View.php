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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Mail;

use Hubzero\View\View as AbstractView;

/**
 * Class for a mail View
 */
class View extends AbstractView
{
	/**
	 * Mail template object
	 * 
	 * @var  object  Hubzero\Mail\Template
	 */
	private $_mailTemplate;

	/**
	 * Constructor
	 *
	 * [!] Override to create instance of mail template
	 * 
	 * @param   array  $config  A named configuration array for object construction.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// create new mail template, loading email.php 
		// in active template falling back to system email.php
		$this->_mailTemplate = new Template();

		// call parent construct
		parent::__construct($config);
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * [!] Override to wrap html view in mail template
	 * 
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 * @return  string  The output of the the template script.
	 */
	public function loadTemplate($tpl = null)
	{
		// hold reference to template passed in
		$template = ($tpl === false) ? null : $tpl;

		// call load template and hold on to content
		$content = parent::loadTemplate($template);

		// if we want to wrap in mail template
		if ($tpl !== false)
		{
			$this->_mailTemplate->setBuffer($content, 'component');
			$content = $this->_mailTemplate->render();
			//$this->_mailTemplate->setBuffer(null, array('type' => 'head', 'name' => 'email'));
			$this->_mailTemplate->setBuffer(null, 'component');
			$this->_mailTemplate->setBuffer(null, 'head');
		}

		// return content
		return $content;
	}

	/**
	 * Include CSS declaration in document head
	 * 
	 * @param   string  $css  CSS string
	 * @return  void
	 */
	public function css($css)
	{
		$this->_mailTemplate->addStyleDeclaration($css);
	}
}
