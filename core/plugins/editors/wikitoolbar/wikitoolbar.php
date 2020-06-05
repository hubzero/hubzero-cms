<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero plugin class for displaying a wiki editor toolbar
 */
class plgEditorWikiToolbar extends \Hubzero\Plugin\Plugin
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
	public function onInit()
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
	 * Display the editor area.
	 *
	 * @param   string   $name     The control name.
	 * @param   string   $content  The contents of the text area.
	 * @param   string   $width    The width of the text area (px or %).
	 * @param   string   $height   The height of the text area (px or %).
	 * @param   int	     $col      The number of columns for the textarea.
	 * @param   int	     $row      The number of rows for the textarea.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string   $asset
	 * @param   object   $author
	 * @param   array    $params   Associative array of editor parameters.
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		if (empty($id))
		{
			$id = $name;
		}
		$id = str_replace(array('[', ']'), '_', $id);

		$col = $col ? (int) $col : 10;
		$row = $row ? (int) $row : 35;

		// Only add "px" to width and height if they are not given as a percentage
		if (is_numeric($width))
		{
			$width .= 'px';
		}
		if (is_numeric($height))
		{
			$height .= 'px';
		}

		if (!isset($params['class']))
		{
			$params['class'] = array();
		}
		if (!is_array($params['class']))
		{
			$params['class'] = array($params['class']);
		}
		if ($cls = $this->params->get('class'))
		{
			$params['class'][] = $cls;
		}
		$params['class'][] = 'wiki-toolbar-content';
		$params['class'] = implode(' ', $params['class']);

		$atts = array();
		foreach ($params as $key => $value)
		{
			$atts[] = $key .'="' . $value . '"';
		}

		$content = preg_replace('/^((?:<|&lt;)!-- \{FORMAT:(?:.*)\} --(?:>|&gt;))/i', '', $content);
		return '<textarea id="' . $id . '" name="' . $name . '" cols="' . $col . '" rows="' . $row . '" ' . implode(' ', $atts) . '>' . $content . '</textarea>' . "\n";
	}
}
