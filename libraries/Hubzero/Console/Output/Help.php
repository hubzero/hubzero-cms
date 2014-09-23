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
 * Output class for rendering help doc
 **/
class Help extends Output
{
	/**
	 * Track whether or not help arguments section has been output yet
	 *
	 * @var bool
	 **/
	private $hasArgumentsSection = false;

	/**
	 * Set variable to forcibly not include arguments section header
	 *
	 * Most likely useful when adding a section to help output, but
	 * never needing an arguments section header to be automatically
	 * generated for you
	 *
	 * @return (object) $this - for method chaining
	 **/
	public function noArgsSection()
	{
		$this->hasArgumentsSection = true;

		return $this;
	}

	/**
	 * Add help output overview section
	 *
	 * @return (object) $this - for method chaining
	 **/
	public function addOverview($text)
	{
		$this
			->addLine(
				'Overview:',
				array(
					'color'       => 'yellow',
					'indentation' => 0
				)
			)
			->addParagraph(
				$text,
				array(
					'indentation' => 2
				)
			)
			->addSpacer();

		return $this;
	}

	/**
	 * Add an argument entry to the help doc
	 *
	 * This is helpful in unifying styles used for help doc
	 *
	 * @param  (string) $argument - actual argument
	 * @param  (string) $details  - description of what it does
	 * @param  (string) $example  - usage example
	 * @param  (string) $required - if it's required, we'll style a bit differently
	 * @return (object) $this     - for method chaining
	 **/
	public function addArgument($argument, $details=NULL, $example=NULL, $required=false)
	{
		if (!$this->hasArgumentsSection)
		{
			$this->addLine(
				'Arguments:',
				array(
					'color'       => 'yellow',
					'indentation' => 0
				)
			);

			$this->hasArgumentsSection = true;
		}

		$this->addLine(
			$argument . (($required) ? ' (*required)' : ''),
			array(
				'color'       => (($required) ? 'red' : 'blue'),
				'indentation' => 2
			)
		);

		if (isset($details))
		{
			$this->addParagraph(
				$details,
				array(
					'indentation' => 4
				)
			);

			if (!isset($example))
			{
				$this->addSpacer();
			}
		}
		if (isset($example))
		{
			$this->addLine(
				$example,
				array(
					'color'       => 'green',
					'indentation' => 4
				)
			)
			->addSpacer();
		}

		return $this;
	}

	/**
	 * Helper method for adding a new section header to helper doc
	 *
	 * @return (object) $this - for method chaining
	 **/
	public function addSection($text)
	{
		$this->addLine(
			$text . ":",
			array(
				'color'       => 'yellow',
				'indentation' => 0
			)
		);

		return $this;
	}
}