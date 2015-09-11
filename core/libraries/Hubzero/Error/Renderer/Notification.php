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

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Hubzero\Notification\Handler;
use Exception;

/**
 * Displays the custom error page when an uncaught exception occurs.
 */
class Notification implements RendererInterface
{
	/**
	 * Notification handler
	 *
	 * @var  object
	 */
	protected $notifier;

	/**
	 * Create a new Notification exception displayer.
	 *
	 * @param   object  $notifier
	 * @return  void
	 */
	public function __construct(Handler $notifier)
	{
		$this->notifier = $notifier;
	}

	/**
	 * Render the error page based on an exception.
	 *
	 * @param   object  $error  The exception for which to render the error page.
	 * @return  void
	 */
	public function render(Exception $error)
	{
		$this->notifier->message($error->getMessage(), ($error->getCode() == 500 ? 'error' : 'warning'));
	}
}
