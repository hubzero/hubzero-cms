<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * PageDown Editor Plugin
 **/
class plgEditorPageDown extends \Hubzer\Plugin\Plugin
{

	const BUTTON_BAR_ID = 'wmd-button-bar';
	const INPUT_ID = 'wmd-input';
	const PREVIEW_ID = 'wmd-preview';

	const CSS_DIRECTORY = 'core/plugins/editors/pagedown/assets/css';
	const JS_DIRECTORY = 'core/plugins/editors/pagedown/assets/js';

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
			'Markdown.Converter.js',
			'Markdown.Sanitizer.js',
			'Markdown.Editor.js'
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
			return str_replace('/administrator', '', Request::base(true)) . "$assetDirectory/$fileName";
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
		$editorDomJs = $this->_buildInitializationJs();

		$editorDomContent = $editorDomElements . $editorDomJs;

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
		$editorDomElements .= '<div id="' . self::BUTTON_BAR_ID . '"></div>';
		$editorDomElements .= $this->_buildTextarea($content, $name, $columns, $rows);
		$editorDomElements .= '<div id="' . self::PREVIEW_ID  . '"></div>';
		$editorDomElements .= '</div>';

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
	protected function _buildTextarea($content, $name, $columns, $rows)
	{
		$textarea = "<textarea name=\"$name\" id=\"" . self::INPUT_ID . "\" cols=\"$columns\" rows=\"$rows\">";
		$textarea .= $content;
		$textarea .= '</textarea>';

		return $textarea;
	}

	/*
	 * Build script to initialize editor
	 *
	 * @return  string
	 */
	protected function _buildInitializationJs()
	{
		$editorDomJs = '<script>';
		$editorDomJs .= "const converter = new Markdown.Converter()\n";
		$editorDomJs .= "const sanitizer = Markdown.getSanitizingConverter()\n";
		$editorDomJs .= "const editor = new Markdown.Editor(converter)\n";
		$editorDomJs .= "editor.run()\n";
		$editorDomJs .= '</script>';

		return $editorDomJs;
	}

}
