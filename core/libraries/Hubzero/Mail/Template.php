<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @since   1.3.1
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
	 * @since   1.3.1
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
