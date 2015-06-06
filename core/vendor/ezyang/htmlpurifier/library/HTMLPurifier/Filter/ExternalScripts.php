<?php

class HTMLPurifier_Filter_ExternalScripts extends HTMLPurifier_Filter
{
	public $name = 'ExternalScripts';
	
	public function preFilter($html, $config, $context)
	{
		$pre_regex   = '#<script(.)*src="([^"]*)"([^>]*)></script>#';
		$pre_replace = '[externalscript src="$2"]';
		return preg_replace($pre_regex, $pre_replace, $html);
	}
	
	public function postFilter($html, $config, $context)
	{
		$pre_regex   = '#\[externalscript src="([^"]*)"\]#';
		$pre_replace = '<script src="$1"></script>';
		return preg_replace($pre_regex, $pre_replace, $html);
	}
}

// vim: et sw=4 sts=4
