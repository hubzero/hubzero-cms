<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Html\Builder\Asset;

/**
 * Helper for outputting icons.
 */
class Icon extends AbstractHelper
{
	/**
	 * Generate asset path
	 *
	 * @param   string  $symbol
	 * @param   bool    $ariahidden
	 * @return  string
	 */
	public function __invoke($symbol, $ariahidden = true)
	{
		return Asset::icon($symbol, $ariahidden);
	}
}
