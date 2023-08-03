<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * PageDown Editor Plugin
 **/
class plgEditorPageDown extends \Hubzero\Plugin\Plugin
{

	const BUTTON_BAR_ID = 'wmd-button-bar';
	const INPUT_ID = 'wmd-input';
	const PREVIEW_ID = 'wmd-preview';

	const CSS_DIRECTORY = 'core/plugins/editors/pagedown/assets/css';
	const JS_DIRECTORY = 'core/plugins/editors/pagedown/assets/js';

	protected $_editorCount = 1;

	/*
	 *  Adds editor scripts & stylesheets to the HTML document
	 *
	 * @return  void
	 */
	public function onInit()
	{
		$this->_addScripts();
		$this->_addStylesheets();
	}

	/*
	 *  Adds editor scripts to the HTML document
	 *
	 * @return  void
	 */
	protected function _addScripts()
	{
		$scriptPaths = $this->_getScriptPaths();

		foreach ($scriptPaths as $scriptPath)
		{
			Document::addScript($scriptPath);
		}
	}

	/*
	 *  Adds editor stylesheets to the HTML document
	 *
	 * @return  void
	 */
	protected function _addStylesheets()
	{
		$stylesheetPaths = $this->_getStylesheetPaths();

		foreach ($stylesheetPaths as $stylesheetPath)
		{
			Document::addStylesheet($stylesheetPath);
		}
	}

	/*
	 *  Generate paths to editor JS files
	 *
	 * @return  array
	 */
	protected function  _getScriptPaths()
	{
		$scriptNames = [
			'turndown.js',
			'htmlToMarkdownConverter.js',
			'Markdown.Converter.js',
			'Markdown.Sanitizer.js',
			'Markdown.Editor.js',
			'editorForm.php?INPUT_ID=' . self::INPUT_ID
		];

		$scriptPaths = $this->_buildAssetPaths($scriptNames, self::JS_DIRECTORY);

		return $scriptPaths;
	}

	/*
	 *  Generate paths to editor stylesheets
	 *
	 * @return  array
	 */
	protected function  _getStylesheetPaths()
	{
		$stylesheetNames = [
			'markdown.css',
		];

		$stylesheetPaths = $this->_buildAssetPaths($stylesheetNames, self::CSS_DIRECTORY);

		return $stylesheetPaths;
	}

	/*
	 *  Generate paths to editor asset files
	 *
	 * @param   array    $assetFileNames    Asset file names
	 * @param   string   $assetDirectory    Subdirectory within /assets
	 * @return  array
	 */
	protected function _buildAssetPaths($assetFileNames, $assetDirectory)
	{
		$assetPaths = array_map(function($fileName) use ($assetDirectory) {
			return str_replace('/administrator', '/', Request::base(true)) . "$assetDirectory/$fileName";
		}, $assetFileNames);

		return $assetPaths;
	}

	/*
	 * Display the editor area
	 *
	 * @param   string   $name     Control name
	 * @param   string   $content  Contents of the text area
	 * @param   string   $width    Width of the text area (px or %)
	 * @param   string   $height   Height of the text area (px or %)
	 * @param   int      $columns  Number of columns for the textarea
	 * @param   int      $rows     Number of rows for the textarea
	 * @param   boolean  $buttons  True and the editor buttons will be displayed
	 * @param   string   $id       Optional ID for the textarea
	 * @param   string   $asset
	 * @param   object   $author
	 * @param   array    $params  Associative array of editor state
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $columns, $rows, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		$id = empty($id) ? $name : $id;
		$columns = $columns ? $columns : 35;
		$rows = $rows ? $rows : 10;
		$this->_parseClasses($params);
		$this->_setDefaultHeight($params, $rows);

		$editorDomElements = $this->_buildEditorDomElements($content, $name, $columns, $rows);

		$editorDomContent = $editorDomElements;

		return $editorDomContent;
	}

	/*
	 * Parse DOM element classes
	 *
	 * @param   array   $params   Editor configuration data
	 * @return  void
	 */
	protected function _parseClasses(&$params)
	{
		if (!isset($params['class']))
		{
			$params['class'] = [];
		}
		elseif (!is_array($params['class']))
		{
			$classes = $params['class'];
			$params['class'] = [];

			foreach (explode(' ', $classes) as $class)
			{
				$params['class'][] = $class;
			}
		}
	}

	/*
	 * Set a default height if needed
	 *
	 * @param   array   $params   Editor configuration data
	 * @param   int     $rows     Number of rows for the textarea
	 * @return  void
	 */
	protected function _setDefaultHeight(&$params, $rows)
	{
		if (!isset($params['height']))
		{
			$defaultHeight = 18 * intval($rows);
			$params['height'] = $defaultHeight . 'px';
		}
	}

	/*
	 * Build editor DOM elements
	 *
	 * @param   string   $content  Contents of the text area
	 * @param   string   $name     Control name
	 * @param   int      $columns  Number of columns for the textarea
	 * @param   int      $rows     Number of rows for the textarea
	 * @return  string
	 */
	protected function _buildEditorDomElements($content, $name, $columns, $rows)
	{
		$editorDomElements = '<div>';
		$idPostfix = ($this->_editorCount > 1) ? ("-" . $this->_editorCount) : '';
		$editorDomElements .= '<div id="' . self::BUTTON_BAR_ID . $idPostfix . '"></div>';
		$editorDomElements .= $this->_buildTextarea($content, $name, $columns, $rows, $idPostfix);
		$editorDomElements .= '</div>';
		$this->_editorCount++;

		return $editorDomElements;
	}

	/*
	 * Build editor textarea
	 *
	 * @param   string   $content  Contents of the text area
	 * @param   string   $name     Control name
	 * @param   int      $columns  Number of columns for the textarea
	 * @param   int      $rows     Number of rows for the textarea
	 * @return  string
	 */
	protected function _buildTextarea($content, $name, $columns, $rows, $idPostfix)
	{
		$textarea = "<textarea name=\"$name\" id=\"" . self::INPUT_ID . $idPostfix . "\" cols=\"$columns\" rows=\"$rows\">";
		$textarea .= $content;
		$textarea .= '</textarea>';

		return $textarea;
	}

	/*
	 * Not applicable in this editor
	 *
	 * @return  void
	 */
	public function onSave()
	{
		//stub
	}

	/*
	 * Not applicable in this editor
	 *
	 * @return  void
	 */
	public function onGetContent()
	{
		//stub
	}

	/*
	 * Not applicable in this editor
	 *
	 * @return  void
	 */
	public function onSetContent()
	{
		//stub
	}

}
