<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
