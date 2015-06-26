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

/**
 * Mail template class.
 * Loads template files for HTML-based emails
 */
class Template extends \JDocumentHTML
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
			if (\JFactory::getApplication()->isAdmin())
			{
				$db = \App::get('db');
				$db->setQuery("SELECT template FROM `#__template_styles` WHERE client_id=0 AND home=1");
				$params['template'] = $db->loadResult();
				$params['directory'] = JPATH_SITE . DS . 'templates';
			}
			else
			{
				$params['template'] = \JFactory::getApplication()->getTemplate();
			}
		}
		if (!isset($params['file']))
		{
			$params['file'] = 'email.php';
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
			$emogrifier = new \Pelago\Emogrifier();
			$emogrifier->setHtml($data);
			//$emogrifier->setCss($css);

			$data = $emogrifier->emogrify();
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
		if (file_exists($directory . '/' . $filename))
		{
			// Store the file path
			$this->_file = $directory . '/' . $filename;

			// Get the file content
			ob_start();
			require $directory . '/' . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		return $contents;
	}
}
