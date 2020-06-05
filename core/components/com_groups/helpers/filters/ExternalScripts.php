<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
