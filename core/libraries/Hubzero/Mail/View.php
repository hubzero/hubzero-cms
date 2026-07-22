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
		// Allow the site's default (home) template to override component email
		// views -- e.g. app/templates/<home>/html/com_forum/emails/digest_html.php.
		// This works even from cron, where no active template is loaded, so email
		// branding no longer requires forking the whole component.
		$this->addMailOverridePath();

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

	/**
	 * Prepend a template-override search path for this email view, resolved from
	 * the site's home template. Lets app/templates/<home>/html/<option>/<name>/
	 * override a component's email views without forking the component, and
	 * without depending on an active template (so it works from cron).
	 *
	 * Purely additive: if no override file exists the core view is used, so
	 * default behavior is unchanged.
	 *
	 * @return  void
	 */
	protected function addMailOverridePath()
	{
		// Only need to add it once, even though loadTemplate() runs per layout
		if ($this->_mailOverrideAdded)
		{
			return;
		}
		$this->_mailOverrideAdded = true;

		// The component this email belongs to (set by the caller as a property)
		$option = isset($this->option) ? (string) $this->option : '';
		$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
		if (!$option)
		{
			return;
		}

		if (!($home = self::homeTemplate()))
		{
			return;
		}

		$path = $home['directory'] . DIRECTORY_SEPARATOR . $home['template']
			. DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $option
			. DIRECTORY_SEPARATOR . $this->getName();

		if (is_dir($path))
		{
			// addTemplatePath() prepends, so an override here wins over the
			// component's own view directory.
			$this->addTemplatePath($path);
		}
	}

	/**
	 * Resolve the site's home template and its base directory (app or core),
	 * mirroring how Hubzero\Mail\Template picks the email wrapper. Cached.
	 *
	 * @return  array|null  ['template' => ..., 'directory' => ...] or null
	 */
	protected static function homeTemplate()
	{
		static $home;

		if ($home !== null)
		{
			return $home ?: null;
		}

		$home = false;

		try
		{
			$tpl = null;

			if (\App::has('template') && ($active = \App::get('template')) && !empty($active->template))
			{
				$tpl = $active->template;
			}
			else
			{
				$db = \App::get('db');
				$db->setQuery("SELECT s.`template` FROM `#__template_styles` AS s INNER JOIN `#__extensions` AS e ON e.`element`=s.`template` WHERE s.`client_id`=0 AND s.`home`=1");
				$tpl = $db->loadResult();
			}

			if ($tpl)
			{
				$directory = is_dir(PATH_APP . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $tpl)
					? PATH_APP . DIRECTORY_SEPARATOR . 'templates'
					: PATH_CORE . DIRECTORY_SEPARATOR . 'templates';

				$home = array('template' => $tpl, 'directory' => $directory);
			}
		}
		catch (\Exception $e)
		{
			$home = false;
		}

		return $home ?: null;
	}

	/**
	 * Whether the mail override path has been added
	 *
	 * @var  boolean
	 */
	private $_mailOverrideAdded = false;
}
