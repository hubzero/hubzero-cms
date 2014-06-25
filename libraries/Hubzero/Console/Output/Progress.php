<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Output;

use Hubzero\Console\Output;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Output class for rendering progress bar/text
 **/
class Progress extends Output
{
	/**
	 * Track content length
	 *
	 * @var int
	 **/
	private $contentLength = 0;

	/**
	 * Save initial message length
	 *
	 * @var int
	 **/
	private $initMessageLength = 0;

	/**
	 * Initialize progress counter
	 *
	 * @param  (string) $initMessage - initial message
	 * @param  (string) $type        - progress type
	 * @param  (int)    $total       - total number of progress points
	 * @return void
	 **/
	public function init($initMessage=null, $type='percentage', $total=null)
	{
		// Force interactivity of this class (this doesn't affect our primary output class)
		$this->makeInteractive();

		if (isset($initMessage))
		{
			// Add the intital message
			$this->addString($initMessage);

			// Track some string lengths
			$this->initMessageLength = strlen($initMessage);
		}

		switch ($type)
		{
			case 'ratio':
				$this->setProgress(0, $total);
				break;
			case 'percentage':
			default:
				// Set current progress to 0
				$this->setProgress('0');
				break;
		}
	}

	/**
	 * Set the current progress val
	 *
	 * @param  (int) $val - progress value
	 * @param  (int) $tot - total value
	 * @return void
	 **/
	public function setProgress($val, $tot=null)
	{
		if ($this->contentLength > 0)
		{
			// Back up current length of content
			$this->backspace($this->contentLength);
		}

		if (!is_null($tot))
		{
			$content = "({$val}/{$tot})";
		}
		else
		{
			// Get new content
			$content = "{$val}%";
		}

		// Save length of content for next call
		$this->contentLength = strlen($content);

		// Add the string
		$this->addString($content);
	}

	/**
	 * Finish progress output
	 *
	 * @return null
	 **/
	public function done()
	{
		// Compute the totall length of the output
		$length = $this->contentLength + $this->initMessageLength;

		// Back up all the way
		$this->backspace($length);
	}

	/**
	 * Do backspaces to overwrite existing text
	 *
	 * @return void
	 **/
	private function backspace($spaces=1)
	{
		echo chr(27) . "[{$spaces}D";
	}
}