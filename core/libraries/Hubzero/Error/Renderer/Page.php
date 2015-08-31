<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Hubzero\Http\Response;
use Exception;

/**
 * Displays the custom error page when an uncaught exception occurs.
 */
class Page implements RendererInterface
{
	/**
	 * Document instance
	 *
	 * @var  object
	 */
	protected $document;

	/**
	 * Template name
	 *
	 * @var  string
	 */
	protected $template;

	/**
	 * Debugging turned on?
	 *
	 * @var  bool
	 */
	protected $debug;

	/**
	 * Create a new exception renderer.
	 *
	 * @param   object  $document  Document instance
	 * @param   string  $template  Template name
	 * @param   bool    $debug     Debugging turned on?
	 * @return  void
	 */
	public function __construct($document, $template = 'system', $debug = false)
	{
		$this->document = $document;
		$this->template = $template;
		$this->debug    = $debug;
	}

	/**
	 * Render the error page based on an exception.
	 *
	 * @param   object  $error  The exception for which to render the error page.
	 * @return  void
	 */
	public function render(Exception $error)
	{
		try
		{
			if (!$this->document)
			{
				// We're probably in an CLI environment
				exit($error->getMessage());
			}

			$this->document->setType('error');

			// Push the error object into the document
			$this->document->setError($error);

			if (ob_get_contents())
			{
				ob_end_clean();
			}

			$this->document->setTitle(\Lang::txt('Error') . ': ' . $error->getCode());

			$path = PATH_APP . DS . 'templates';
			if (!is_dir($path . DS . $this->template))
			{
				$path = PATH_CORE . DS . 'templates';
			}

			$data = $this->document->render(
				false,
				array(
					'template'  => $this->template,
					'directory' => $path,
					'debug'     => $this->debug
				)
			);

			// Failsafe to get the error displayed.
			if (empty($data))
			{
				exit($error->getMessage() . ' in ' . $error->getFile() . ':' . $error->getLine());
			}
			else
			{
				$status = $error->getCode() ? $error->getCode() : 500;
				$status = ($status < 100 || $status >= 600) ? 500 : $status;

				$response = new Response($data, $status);
				$response->send();

				exit();
			}
		}
		catch (Exception $e)
		{
			$plain = new Plain($this->debug);
			$plain->render($e);
		}
	}
}
