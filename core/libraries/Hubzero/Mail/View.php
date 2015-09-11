<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
