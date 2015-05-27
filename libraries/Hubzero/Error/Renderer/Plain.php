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

/**
 * Displays plain error info when an uncaught exception occurs.
 */
class Plain implements RendererInterface
{
	/**
	 * Display the given exception to the user.
	 *
	 * @param   object  $exception
	 * @return  void
	 */
	public function render(Exception $error)
	{
		$response = $error->getCode() . ' - ' . $error->getMessage() . "\n\n";

		$backtrace = $error->getTrace();

		if (is_array($backtrace))
		{
			for ($i = count($backtrace) - 1; $i >= 0; $i--)
			{
				$response .= '[' . $j . '] ' . $backtrace[$i]['class'] . $backtrace[$i]['type'] . $backtrace[$i]['function'] . '();';
				$response .= $backtrace[$i]['function'] . '();';

				if (isset($backtrace[$i]['file']))
				{
					$response .= $backtrace[$i]['file'] . ':' . $backtrace[$i]['line'];
				}
				else
				{
					$response .= '...';
				}

				$response .= "\n\n";

				$j++;
			}
		}

		echo $response;

		exit();
	}
}
