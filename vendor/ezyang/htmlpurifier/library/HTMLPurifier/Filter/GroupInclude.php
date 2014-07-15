<?php

class HTMLPurifier_Filter_GroupInclude extends HTMLPurifier_Filter
{
	public $name = 'GroupInclude';
	
	public function preFilter($html, $config, $context)
	{
		$pre_regex   = '#<group:include([^>]*)/>#';
		$pre_replace = '[group:include$1]';
		return preg_replace($pre_regex, $pre_replace, $html);
	}
	
	public function postFilter($html, $config, $context)
	{
		$pre_regex   = '#\[group:include([^\]]*)\]#';
		$pre_replace = '<group:include$1/>';
		return preg_replace($pre_regex, $pre_replace, $html);
	}
}

// vim: et sw=4 sts=4
