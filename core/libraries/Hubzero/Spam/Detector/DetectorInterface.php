<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Detector;

/**
 * Spam detector interface
 */
interface DetectorInterface
{
	/**
	 * Run content through spam detection
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data);

	/**
	 * Return any message the service may have
	 *
	 * @return  string
	 */
	public function message();
}
