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

			$params['template']  = $result->template;
			$params['directory'] = ($result->protected ? PATH_CORE : PATH_APP) . DS . 'templates';
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
