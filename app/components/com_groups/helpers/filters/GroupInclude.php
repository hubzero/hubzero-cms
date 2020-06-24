<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class HTMLPurifier_Filter_GroupInclude extends HTMLPurifier_Filter
{
	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'GroupInclude';

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
		$pre_regex   = '#<group:include([^>]*)/>#';
		$pre_replace = '[group:include$1]';
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
		$pre_regex   = '#\[group:include([^\]]*)\]#';
		$pre_replace = '<group:include$1/>';
		return preg_replace($pre_regex, $pre_replace, $html);
	}
}
