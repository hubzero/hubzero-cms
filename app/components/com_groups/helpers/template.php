<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

use Component;
use App;

class Template extends Document
{
	/**
	 * Error bag
	 *
	 * @var  mixed
	 */
	public $error = null;

	/**
	 * Array of group include tags allowed
	 * (all tags)
	 *
	 * @var  array
	 */
	public $allowed_tags = array(
		'module',
		'modules',
		'toolbar',
		'menu',
		'content',
		'googleanalytics',
		'stylesheet',
		'script'
	);

	/**
	 * Override parse template to get document content
	 *
	 * @return  object  $this
	 */
	public function parse()
	{
		// check to make sure we have group
		if (!$this->get('group'))
		{
			App::abort(406, 'Missing Needed Hubzero Group Object');
		}

		// define base path
		$params = Component::params('com_groups');
		$base   = $params->get('uploadpath', '/site/groups');
		$base   = DS . trim($base, DS) . DS . $this->group->get('gidNumber') . DS . 'template' . DS;

		// fetch template file (sets document for parsing)
		$this->_fetch($base);

		// call parse
		return parent::parse();
	}

	/**
	 * Return Content
	 *
	 * @param   boolean  $echo  Echo document or return?
	 * @return  mixed    String or echos results
	 */
	public function output($echo = false)
	{
		// parse php code
		ob_start();
		eval("?> ".$this->get('document')." <?php ");
		$this->set('document', ob_get_clean());

		// run output declared in parent
		return parent::output($echo);
	}

	/**
	 * Fetches Template File
	 *
	 * @param   string  $base
	 * @return  object  $this
	 */
	private function _fetch($base)
	{
		// only fetch template if we dont already have one
		// or if we are forcing it to fetch again
		if ($this->get('document') === null)
		{
			// var to hold our final template
			$template = null;

			// build array of possible page templates to load
			$possibleTemplates = array(
				$base . 'index.php',
				$base . 'default.php'
			);

			// if we have an active page, add other template possibilities
			if ($this->page !== null)
			{
				$possibleTemplates[] = $base . 'page.php';
				$possibleTemplates[] = $base . 'page-' . $this->page->get('id') . '.php';
				$possibleTemplates[] = $base . 'page-' . $this->page->get('alias') . '.php';
				$possibleTemplates[] = $base . $this->page->get('template') . '.php';
				$possibleTemplates = array_reverse($possibleTemplates);
			}

			// get the template we want to load
			foreach ($possibleTemplates as $possibleTemplate)
			{
				if (file_exists(PATH_APP . $possibleTemplate))
				{
					$template = $possibleTemplate;
					break;
				}
			}

			// do we have a problem houston?
			if ($this->get('error') !== null)
			{
				$template = $base . 'error.php';
			}

			//we we dont have a super group template
			if ($template === null)
			{
				App::abort(500, 'Missing "Super Group" template file.');
				return;
			}

			// load the template & set docuement
			$this->set('document', $this->_load(PATH_APP . $template));
		}

		// return this for chainability
		return $this;
	}


	/**
	 * Does the group have a specified template?
	 *
	 * @param   object   $group
	 * @param   string   $template
	 * @return  boolean
	 */
	public static function hasTemplate($group, $template)
	{
		// define base path
		$params = Component::params('com_groups');
		$base   = $params->get('uploadpath', '/site/groups');
		$base   = DS . trim($base, DS) . DS . $group->get('gidNumber') . DS . 'template' . DS;

		// add php extension
		if (substr($template, -4, 4) != '.php')
		{
			$template .= '.php';
		}

		// does the file exist?
		return file_exists(PATH_APP . $base . $template);
	}

	/**
	 * Load Template File
	 *
	 * @param   string  $template
	 * @return  string
	 */
	private function _load($template)
	{
		ob_start();
		require_once $template;
		$contents = ob_get_clean();
		return $contents;
	}
}
