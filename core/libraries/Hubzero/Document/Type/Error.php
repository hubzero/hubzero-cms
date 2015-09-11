<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;
use Exception;
use Request;

/**
 * Error document class for parsing and displaying an error page
 *
 * Inspired by Joomla's JDocumentError class
 */
class Error extends Base
{
	/**
	 * Error Object
	 *
	 * @var  object
	 */
	protected $error;

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of attributes
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Set mime type
		$this->mime = 'text/html';

		// Set document type
		$this->type = 'error';
	}

	/**
	 * Set error object
	 *
	 * @param   object   $error  Error object to set
	 * @return  boolean  True on success
	 */
	public function setError($error, $key = null)
	{
		if ($error instanceof Exception)
		{
			$this->error = $error;
			return true;
		}

		return false;
	}

	/**
	 * Render the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  string   The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		// If no error object is set return null
		if (!isset($this->error))
		{
			return;
		}

		// Set the status header
		//\App::get('response')->headers->set('status', $this->error->getCode() . ' ' . str_replace("\n", ' ', $this->error->getMessage()));

		$file = 'error.php';

		// Check template
		$directory = isset($params['directory']) ? $params['directory'] : PATH_CORE . '/templates';
		$template  = isset($params['template'])  ? ltrim(preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $params['template']), '.') : 'system';

		if (!file_exists($directory . DS . $template . DS . $file))
		{
			$directory = PATH_CORE . '/templates';
			$template = 'system';
		}

		// Set variables
		$this->baseurl  = (isset($params['baseurl']) ? $params['baseurl'] : rtrim(\Request::root(true), '/') . rtrim(substr(dirname($directory), strlen(PATH_ROOT)), '/'));
		$this->template = $template;
		$this->debug    = isset($params['debug']) ? $params['debug'] : false;

		// Load
		$data = $this->loadTemplate($directory . DS . $template, $file);

		parent::render();

		return $data;
	}

	/**
	 * Load a template file
	 *
	 * @param   string  $directory  The name of the template
	 * @param   string  $filename   The actual filename
	 * @return  string  The contents of the template
	 */
	protected function loadTemplate($directory, $filename)
	{
		$contents = '';

		// Check to see if we have a valid template file
		if (file_exists($directory . DS . $filename))
		{
			// Store the file path
			$this->file = $directory . DS . $filename;

			// Get the file content
			ob_start();
			require_once $directory . DS . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		return $contents;
	}

	/**
	 * Render the backtrace
	 *
	 * @return  string  The contents of the backtrace
	 */
	public function renderBacktrace()
	{
		$contents  = null;
		$backtrace = $this->error->getTrace();

		if (is_array($backtrace))
		{
			ob_start();

			$j = 1;

			$html = array();
			$html[] = '<table class="backtrace">';
			$html[] = '	<caption>Call stack</caption>';
			$html[] = '	<thead>';
			$html[] = '		<tr>';
			$html[] = '			<th scope="col">#</th>';
			$html[] = '			<th scope="col">Function</th>';
			$html[] = '			<th scope="col">Location</th>';
			$html[] = '		</tr>';
			$html[] = '	</thead>';
			$html[] = '	<tbody>';
			$html[] = '		<tr>';
			$html[] = '			<th scope="row">0</th>';
			$html[] = '			<td><span class="msg">!! ' . $this->error->getMessage() . ' !!</span></td>';
			$html[] = '			<td><span class="fl">' . $this->rooted($this->error->getFile()) . '</span>:<span class="ln">' . $this->error->getLine() . '</span></td>';
			$html[] = '		</tr>';
			for ($i = count($backtrace) - 1; $i >= 0; $i--)
			{
				$html[] = '		<tr>';
				$html[] = '			<th scope="row">' . $j . '</th>';
				if (isset($backtrace[$i]['class']))
				{
					$html[] = '			<td><span class="cls">' . $backtrace[$i]['class'] . '</span><span class="opn">' . $backtrace[$i]['type'] . '</span><span class="mtd">' . $backtrace[$i]['function'] . '</span>()</td>';
				}
				else
				{
					$html[] = '			<td><span class="fnc">' . $backtrace[$i]['function'] . '</span>()</td>';
				}
				if (isset($backtrace[$i]['file']))
				{
					$html[] = '			<td><span class="fl">' . $this->rooted($backtrace[$i]['file']) . '</span>:<span class="ln">' . $backtrace[$i]['line'] . '</span></td>';
				}
				else
				{
					$html[] = '			<td>&#160;</td>';
				}
				$html[] = '		</tr>';
				$j++;
			}
			$html[] = '	</tbody>';
			$html[] = '</table>';

			echo "\n" . implode("\n", $html) . "\n";
			$contents = ob_get_contents();
			ob_end_clean();
		}
		return $contents;
	}

	/**
	 * Strip root path off to shorten lines some
	 *
	 * @param   string  $path
	 * @return  string
	 */
	private function rooted($path)
	{
		if (substr($path, 0, strlen(PATH_ROOT)) == PATH_ROOT)
		{
			$path = 'ROOT' . substr($path, strlen(PATH_ROOT));
		}
		return $path;
	}
}
