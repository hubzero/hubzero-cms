<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Mail;

use Hubzero\Document\Type\Html;
use App;

/**
 * Mail template class.
 * Loads template files for HTML-based emails
 */
class Template extends Html
{
	/**
	 * Outputs the template to the browser.
	 *
	 * [!] Overloaded to remove portion that sets headers.
	 *
	 * @param   boolean  $caching  If true, cache the output
	 * @param   array    $params   Associative array of attributes
	 * @return  The rendered data
	 */
	public function render($caching = false, $params = array())
	{
		if (!isset($params['template']))
		{
			if (App::isAdmin())
			{
				$db = App::get('db');
				$db->setQuery("SELECT s.`template`, e.protected FROM `#__template_styles` AS s INNER JOIN `#__extensions` AS e ON e.`element`=s.`template` WHERE s.`client_id`=0 AND s.`home`=1");
				$result = $db->loadObject();
			}
			else
			{
				$result = App::get('template');
			}

			$params['template'] = $result->template;
			if (is_dir(PATH_APP . DS . 'templates' . DS . $result->template))
			{
				$params['directory'] = PATH_APP . DS . 'templates';
			}
			else
			{
				$params['directory'] = PATH_CORE . DS . 'templates';
			}
		}

		if (!isset($params['file']))
		{
			$params['file'] = 'email.php';
		}

		if (!file_exists($params['directory'] . DS . $params['template'] . DS . $params['file']))
		{
			$params['template']  = 'system';
			$params['directory'] = PATH_CORE . DS . 'templates';
		}

		$this->_caching = $caching;

		if (!empty($this->_template))
		{
			$data = $this->_renderTemplate();
		}
		else
		{
			$this->parse($params);
			$data = $this->_renderTemplate();
		}

		if (class_exists('\Pelago\Emogrifier') && $data)
		{
			$data = str_replace('&#', '{_ANDNUM_}', $data);
			$emogrifier = new \Pelago\Emogrifier();
			$emogrifier->preserveEncoding = true;
			$emogrifier->setHtml($data);
			//$emogrifier->setCss($css);

			$data = $emogrifier->emogrify();
			$data = str_replace('{_ANDNUM_}', '&#', $data);
		}

		return $data;
	}

	/**
	 * Load a template file
	 *
	 * [!] Overloaded to remove automatic favicon injection
	 *
	 * @param   string  $directory  The name of the template
	 * @param   string  $filename   The actual filename
	 * @return  string  The contents of the template
	 */
	protected function _loadTemplate($directory, $filename)
	{
		$contents = '';

		// Check to see if we have a valid template file
		if (file_exists($directory . DS . $filename))
		{
			// Store the file path
			$this->_file = $directory . DS . $filename;

			// Get the file content
			ob_start();
			require $directory . DS . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		return $contents;
	}
}
