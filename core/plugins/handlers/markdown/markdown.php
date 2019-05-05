<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for MD file handling
 */
class plgHandlersMarkdown extends Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Determines if the given collection can be handled by this plugin
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
	 * @return  void
	 **/
	public function canHandle(Hubzero\Filesystem\Collection $collection)
	{
		$need = [
			'md' => 1
		];

		// Check extension to make sure we can proceed
		if (!$collection->hasExtensions($need))
		{
			return false;
		}

		return true;
	}

	/**
	 * Handles view events for files
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
	 * @return  void
	 **/
	public function onHandleView(Hubzero\Filesystem\Collection $collection)
	{
		if (!$this->canHandle($collection))
		{
			return false;
		}

		$file = $collection->findFirstWithExtension('md');

		if (!$file || !($file instanceof Hubzero\Filesystem\File))
		{
			return false;
		}

		$source = rtrim($file->read());

		$md = array(
			'block/CodeTrait.php',
			'block/FencedCodeTrait.php',
			'block/HeadlineTrait.php',
			'block/HtmlTrait.php',
			'block/ListTrait.php',
			'block/QuoteTrait.php',
			'block/RuleTrait.php',
			'block/TableTrait.php',
			'inline/CodeTrait.php',
			'inline/EmphStrongTrait.php',
			'inline/LinkTrait.php',
			'inline/StrikeoutTrait.php',
			'inline/UrlLinkTrait.php',
			'Parser.php',
			'Markdown.php',
			'MarkdownExtra.php',
			'GithubMarkdown.php'
		);
		foreach ($md as $mdfile)
		{
			include_once __DIR__ . '/markdown/' . $mdfile;
		}

		$cls = '\\cebe\\markdown\\' . $this->params->get('style', 'Markdown');

		$parser = new $cls();

		$rendered = $parser->parse($source);

		$view = $this->view('view', 'markdown');

		if (!$rendered)
		{
			$view->setError(Lang::txt('PLG_HANDLERS_MARKDOWN_ERROR_RENDER_FAILED'));
			$rendered = $source;
		}

		$view->rendered = $rendered;

		return $view;
	}
}
