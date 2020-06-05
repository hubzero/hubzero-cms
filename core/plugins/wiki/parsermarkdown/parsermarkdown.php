<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki plugin class for loading the default parser
 */
class plgWikiParsermarkdown extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Holds the parser for re-use
	 *
	 * @var  object
	 */
	public $parser = null;

	/**
	 * Get the wiki parser, creating a new one if not already existing or $getnew is set
	 *
	 * @param   array    $config  Options for initializing a parser
	 * @param   boolean  $getnew  Init a new parser?
	 * @return  object
	 */
	public function onGetWikiParser($config, $getnew=false)
	{
		if (!is_object($this->parser) || $getnew)
		{
			$path = dirname(__FILE__);

			if (is_file($path . DS . 'parser.php'))
			{
				include_once $path . DS . 'parser.php';

				$config['option']    = (isset($config['option']))    ? $config['option']    : 'com_wiki';
				$config['scope']     = (isset($config['scope']))     ? $config['scope']     : '';
				$config['pagename']  = (isset($config['pagename']))  ? $config['pagename']  : '';
				$config['pageid']    = (isset($config['pageid']))    ? $config['pageid']    : 0;
				$config['filepath']  = (isset($config['filepath']))  ? $config['filepath']  : '';
				$config['domain']    = (isset($config['domain']))    ? $config['domain']    : null;
				$config['domain_id'] = (isset($config['domain_id'])) ? $config['domain_id'] : null;
				$config['url']       = (isset($config['url']))       ? $config['url']       : null;
				$config['loglinks']  = (isset($config['loglinks']))  ? $config['loglinks']  : null;

				$config['style']     = (isset($config['style'])      ? $config['style']     : $this->params->get('style', 'GithubMarkdown'));

				$this->parser = new MarkdownParser($config);
			}
		}

		return $this->parser;
	}

	/**
	 * Turns wiki markup to HTML
	 *
	 * @param   string   $text       Text to convert
	 * @param   array    $config     Options for initializing a parser
	 * @param   boolean  $fullparse  Do a full parse or ignore some things like macros?
	 * @param   boolean  $getnew     Init a new parser?
	 * @return  string
	 */
	public function onWikiParseText($text, $config, $fullparse=true, $getnew=false)
	{
		$parser = $this->onGetWikiParser($config, $getnew);
		$config['camelcase'] = (isset($config['camelcase']) ? $config['camelcase'] : 1);

		return is_object($parser) ? $parser->parse("\n" . $text, $fullparse, 0, $config['camelcase']) : $text;
	}
}
