<?php

class HTMLPurifier_Filter_Php extends HTMLPurifier_Filter
{
	public $name = 'Php';
	
	public function preFilter($html, $config, $context)
	{
		$html = str_replace('<?php', '[php]', $html);
		$html = str_replace('<?', '[php]', $html);
		$html = str_replace('?>', '[/php]', $html);
		return $html;
	}
	
	public function postFilter($html, $config, $context)
	{
		$html = preg_replace_callback(
					"#\\[php]([^\\[]*)\\[/php]#us",  
					function($matches) {
						return "<?php\n" . trim(html_entity_decode($matches[1])) . "\n?>";
					},
					$html);
		return $html;		
	}
}

// vim: et sw=4 sts=4
