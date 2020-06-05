<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for displaying a wiki editor toolbar
 */
class plgWikiEditortoolbar extends \Hubzero\Plugin\Plugin
{
	/**
	 * Flag for if scripts need to be pushed to the document or not
	 *
	 * @var  boolean
	 */
	private static $pushscripts = true;

	/**
	 * Initiate the editor. Push scripts to document if needed
	 *
	 * @return  string
	 */
	public function onInitEditor()
	{
		if (self::$pushscripts)
		{
			$this->css();
			$this->js();

			self::$pushscripts = false;
		}

		return '';
	}

	/**
	 * Display a wiki editor
	 *
	 * @param   string   $name     Name of field
	 * @param   string   $id       ID for field
	 * @param   string   $content  Field content
	 * @param   string   $cls      Field class
	 * @param   integer  $col      Number of columns
	 * @param   integer  $row      Number of rows
	 * @return  string   HTML
	 */
	public function onDisplayEditor($name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)
	{
		$content = preg_replace('/^((?:<|&lt;)!-- \{FORMAT:(?:.*)\} --(?:>|&gt;))/i', '', $content);

		$cls = ($cls) ? 'wiki-toolbar-content ' . $cls : 'wiki-toolbar-content';

		return '<textarea id="' . $id . '" name="' . $name . '" cols="' . $col . '" rows="' . $row . '" class="' . $cls . '">' . $content . '</textarea>' . "\n";
	}
}
