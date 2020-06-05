<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class HTMLPurifier_Filter_Php extends HTMLPurifier_Filter
{
	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'Php';

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
		$html = str_replace('<?php', '[php]', $html);
		$html = str_replace('<?', '[php]', $html);
		$html = str_replace('?>', '[/php]', $html);
		return $html;
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
		$html = preg_replace_callback(
			"#\\[php]([^\\[]*)\\[/php]#us",
			function($matches)
			{
				return "<?php\n" . trim(html_entity_decode($matches[1])) . "\n?>";
			},
			$html
		);
		return $html;
	}
}
