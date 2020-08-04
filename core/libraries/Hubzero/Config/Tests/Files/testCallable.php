<?php
/**
 * @codeCoverageIgnore
 */
// @codeCoverageIgnoreStart
class ConfigGetter
{
	public function getConfig()
	{
		$config = array(
			'app' => array(
				'application_env' => 'development',
				'editor' => 'ckeditor',
				'list_limit' => '25',
				'helpurl' => 'English (GB) - HUBzero help',
				'debug' => '1',
				'debug_lang' => '0',
				'sef' => '1',
				'sef_rewrite' => '1',
				'sef_suffix' => '0',
				'sef_groups' => '0',
				'feed_limit' => '10',
				'feed_email' => 'author',
			),
			'seo' => array(
				'sef' => '1',
				'sef_groups' => '0',
				'sef_rewrite' => '1',
				'sef_suffix' => '0',
				'unicodeslugs' => '0',
				'sitename_pagetitles' => '0',
			),
		);

		return $config;
	}
}

return array(new ConfigGetter(), 'getConfig');
// @codeCoverageIgnoreEnd
