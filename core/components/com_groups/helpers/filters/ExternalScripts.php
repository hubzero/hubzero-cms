<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

class HTMLPurifier_Filter_ExternalScripts extends HTMLPurifier_Filter
{
	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'ExternalScripts';

	/**
	 * Pre-filter hook
	 *
	 * @param   string  $html
	 * @param   array   $config
	 * @param   string  $context
	 * @return  string
	 */
	public function preFilter($html, $config, $context)
	{
		$pre_regex   = '#<script(.)*src="([^"]*)"([^>]*)></script>#';
		$pre_replace = '[externalscript src="$2"]';
		return preg_replace($pre_regex, $pre_replace, $html);
	}

	/**
	 * Post-filter hook
	 *
	 * @param   string  $html
	 * @param   array   $config
	 * @param   string  $context
	 * @return  string
	 */
	public function postFilter($html, $config, $context)
	{
		$pre_regex   = '#\[externalscript src="([^"]*)"\]#';
		$pre_replace = '<script src="$1"></script>';
		return preg_replace($pre_regex, $pre_replace, $html);
	}
}
