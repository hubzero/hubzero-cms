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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
Html::behavior('core');

/**
 * CKEditor Plugin
 */
class plgEditorCkeditor extends \Hubzero\Plugin\Plugin
{
	/**
	 * Base path for editor files
	 */
	protected $_basePath = 'core/plugins/editors/ckeditor/assets/';

	/**
	 * Method to handle the onInitEditor event.
	 *  - Initialises the Editor
	 *
	 * @return  string JavaScript Initialization string
	 */
	public function onInit()
	{
		// add ckeditor stylesheet
		$this->css();

		// add ckeditor
		Document::addScript(str_replace('/administrator', '', Request::base(true)) . '/' . $this->_basePath . 'ckeditor.js' );
		Document::addScript(str_replace('/administrator', '', Request::base(true)) . '/' . $this->_basePath . 'adapters/jquery.js' );
	}

	/**
	 * Copy editor content to form field.
	 *
	 * Not applicable in this editor.
	 *
	 * @return  void
	 */
	public function onSave()
	{
		$js = "for (instance in CKEDITOR.instances) {
				CKEDITOR.instances[instance].fire('beforeSave');
				CKEDITOR.instances[instance].updateElement();
			}";
		return $js;
	}

	/**
	 * Get the editor content.
	 *
	 * @param   string $id The id of the editor field.
	 * @return  string
	 */
	public function onGetContent($id)
	{
		$this->js("
			function getEditorContent(id) {
				CKEDITOR.instances['$id'].updateElement();
				return document.getElementById('$id').value;
			}
		");
		return "getEditorContent('$id');\n";
	}

	/**
	 * Set the editor content.
	 *
	 * @param   string $id   The id of the editor field.
	 * @param   string $html The content to set.
	 * @return  string
	 */
	public function onSetContent($id, $html)
	{
		return "CKEDITOR.instances['$id'].setData($html);\n"; //" document.getElementById('$id').value = '$html';\n";
	}

	/**
	 * @param	string	$id
	 * @return	string
	 */
	public function onGetInsertMethod($id)
	{
		$js = "
			function jInsertEditorText( text, editor ) {
				CKEDITOR.instances[editor].updateElement();
				var content = document.getElementById(editor).value;
				content = content + text;
				CKEDITOR.instances[editor].setData(content);
			}
		";

		Document::addScriptDeclaration($js);

		return true;
	}

	/**
	 * Display the editor area.
	 *
	 * @param   string  $name    The control name.
	 * @param   string  $html    The contents of the text area.
	 * @param   string  $width   The width of the text area (px or %).
	 * @param   string  $height  The height of the text area (px or %).
	 * @param   int     $col     The number of columns for the textarea.
	 * @param   int     $row     The number of rows for the textarea.
	 * @param   boolean $buttons True and the editor buttons will be displayed.
	 * @param   string  $id      An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string  $asset
	 * @param   object  $author
	 * @param   array   $params  Associative array of editor parameters.
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		// make sure we have an id too
		if (empty($id))
		{
			$id = $name;
		}

		$col = $col ?: 35;
		$row = $row ?: 10;

		if (!isset($params['class']))
		{
			$params['class'] = array();
		}
		if (!is_array($params['class']))
		{
			//$params['class'] = array($params['class']);
			$cls = $params['class'];
			$params['class'] = array();
			foreach ($this->_split(' ', $cls) as $piece)
			{
				$params['class'][] = $piece;
			}
		}
		/*if ($cls = $this->params->get('class'))
		{
			foreach ($this->_split(' ', $cls) as $piece)
			{
				$params['class'][] = $piece;
			}
		}*/
		$params['class'][] = 'ckeditor-content';

		// Set default height to a rough approximation of the height
		// of the textarea (rows * 1.5em of 12px font)
		if (!isset($params['height']))
		{
			$params['height'] = (18 * intval($row)) . 'px';
		}

		// build config & json encode
		$config = json_encode($this->_buildConfig($params));

		// fix script and php protected source
		//$config = str_replace('"\\/<group:include([^\\/]*)\\/>\\/g"', '/<group:include([^/]*)/>/g', $config);
		$config = str_replace('"\\/<script[^>]*>(.|\\\\n)*<\\\\\\/script>\\/ig"', '/<script[^>]*>(.|\n)*<\/script>/ig', $config);
		$config = str_replace('"\\/<\\\\?[\\\\s\\\\S]*?\\\\?>\\/g"', '/<\?[\s\S]*?\?>/g', $config);
		$config = str_replace('"\/<group:include([^>]*)\\\\\/>\/g"', '/<group:include([^>]*)\\/>/g', $config);
		$config = str_replace('"\/{xhub:([^}]*)}\/gi"', '/{xhub:([^}]*)}/gi', $config);

		// script to actually make ckeditor
		$script  = '<script type="text/javascript">';
		$script .= 'if (typeof(jQuery) !== "undefined") {';
		$script .= 'jQuery(document).ready(function() { jQuery("#'.$id.'").ckeditor(function() {}, '.$config.'); });';
		$script .= 'jQuery(document).on("ajaxLoad", function() { jQuery("#'.$id.'").ckeditor(function() {}, '.$config.'); });';
		$script .= '}';
		$script .= '</script>';

		$params['class'] = implode(' ', $params['class']);

		$atts = array();
		foreach ($params as $key => $value)
		{
			if (is_array($value))
			{
				$value = implode(';', $value);
			}
			$atts[] = $key .'="' . $value . '"';
		}

		// output html and script
		$editor  = '<textarea name="' . $name . '" id="' . $id . '" ' . ($row ? 'rows="' . $row . '"' : '') . ' ' . ($col ? 'cols="' . $col . '"' : '') . ' ' . implode(' ', $atts) . '>' . $content . '</textarea>' . $script;
		if (App::isAdmin())
		{
			$editor .= $this->_displayButtons($id, $buttons, $asset, $author);
		}
		return $editor;
	}

	/**
	 *
	 * @return  string
	 */
	private function _displayButtons($name, $buttons, $asset, $author)
	{
		// Load modal popup behavior
		Html::behavior('modal', 'a.modal-button');

		$return = '';
		$results[] = $this->onGetInsertMethod($name);

		foreach ($results as $result)
		{
			if (is_string($result) && trim($result))
			{
				$return .= $result;
			}
		}

		if (is_array($buttons) || (is_bool($buttons) && $buttons))
		{
			$results = $this->_subject->getButtons($name, $buttons, $asset, $author);

			// This will allow plugins to attach buttons or change the behavior on the fly using AJAX
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";

			foreach ($results as $button)
			{
				// Results should be an object
				if ($button->get('name'))
				{
					$modal   = ($button->get('modal')) ? ' class="modal-button"' : null;
					$href    = ($button->get('link')) ? ' href="'.Request::base().$button->get('link').'"' : null;
					$onclick = ($button->get('onclick')) ? ' onclick="'.$button->get('onclick').'"' : 'onclick="IeCursorFix(); return false;"';
					$title   = ($button->get('title')) ? $button->get('title') : $button->get('text');
					$return .= '<div class="button2-left"><div class="' . $button->get('name') . '"><a' . $modal . ' title="' . $title . '"' . $href . $onclick . ' rel="' . $button->get('options') . '">' . $button->get('text') . "</a></div></div>\n";
				}
			}

			$return .= "</div>\n";
		}

		return $return;
	}

	/**
	 * Build a config object
	 *
	 * @param   string $delimiter
	 * @param   string $input
	 * @return  array
	 */
	private function _split($delimiter, $input)
	{
		$even = array();

		if (is_array($input))
		{
			foreach ($input as $el)
			{
				$even = array_merge($even, $this->_split($delimiter, $el));
			}
		}
		else
		{
			$pieces = explode($delimiter, $input);
			$pieces = array_map('trim', $pieces);

			$even = array_merge($even, $pieces);
		}
		return $even;
	}

	/**
	 * Build a config object
	 *
	 * @return  object stdClass
	 */
	private function _buildConfig($params = array())
	{
		static $template;

		// merge incoming params with
		$this->params->merge($params);

		// object to hold our final config
		$config                                = new stdClass;
		$config->startupMode                   = 'wysiwyg';
		$config->tabSpaces                     = 4;
		$config->height                        = '200px';
		$config->hubzeroAutogrow_autoStart     = true;
		$config->hubzeroAutogrow_minHeight     = 200;
		$config->hubzeroAutogrow_maxHeight     = 1000;
		$config->toolbarCanCollapse            = true;
		$config->extraPlugins                  = 'tableresize,iframedialog,hubzeroequation,hubzerogrid,hubzeromacro,hubzerohighlight';
		$config->removePlugins                 = '';
		$config->resize_enabled                = true;
		$config->emailProtection               = '';
		$config->protectedSource               = array('/<group:include([^>]*)\\/>/g', '/{xhub:([^}]*)}/gi', '/<map[^>]*>(.|\n)*<\/map>/ig', '/<area([^>]*)\/?>/ig');
		$config->extraAllowedContent           = 'img(*)[*]; style(*)[*]; mark(*)[*]; span(*)[*]; map(*)[*]; area(*)[*]; input(*)[type]; *(*)[*]{*}';
		$config->specialChars                  = array('!', '&quot;', '#', '$', '%', '&amp;', "'", '(', ')', '*', '+', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '&lt;', '=', '&gt;', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', "&euro;", "&lsquo;", "&rsquo;", "&ldquo;", "&rdquo;", "&ndash;", "&mdash;", "&iexcl;", "&cent;", "&pound;", "&curren;", "&yen;", "&brvbar;", "&sect;", "&uml;", "&copy;", "&ordf;", "&laquo;", "&not;", "&reg;", "&macr;", "&deg;", "&sup2;", "&sup3;", "&acute;", "&micro;", "&para;", "&middot;", "&cedil;", "&sup1;", "&ordm;", "&raquo;", "&frac14;", "&frac12;", "&frac34;", "&iquest;", "&Agrave;", "&Aacute;", "&Acirc;", "&Atilde;", "&Auml;", "&Aring;", "&AElig;", "&Ccedil;", "&Egrave;", "&Eacute;", "&Ecirc;", "&Euml;", "&Igrave;", "&Iacute;", "&Icirc;", "&Iuml;", "&ETH;", "&Ntilde;", "&Ograve;", "&Oacute;", "&Ocirc;", "&Otilde;", "&Ouml;", "&times;", "&Oslash;", "&Ugrave;", "&Uacute;", "&Ucirc;", "&Uuml;", "&Yacute;", "&THORN;", "&szlig;", "&agrave;", "&aacute;", "&acirc;", "&atilde;", "&auml;", "&aring;", "&aelig;", "&ccedil;", "&egrave;", "&eacute;", "&ecirc;", "&euml;", "&igrave;", "&iacute;", "&icirc;", "&iuml;", "&eth;", "&ntilde;", "&ograve;", "&oacute;", "&ocirc;", "&otilde;", "&ouml;", "&divide;", "&oslash;", "&ugrave;", "&uacute;", "&ucirc;", "&uuml;", "&yacute;", "&thorn;", "&yuml;", "&OElig;", "&oelig;", "&#372;", "&#374", "&#373", "&#375;", "&sbquo;", "&#8219;", "&bdquo;", "&hellip;", "&trade;", "&#9658;", "&bull;", "&rarr;", "&rArr;", "&hArr;", "&diams;", "&asymp;", "&Omega;");
		$config->disableNativeSpellChecker     = false;
		$config->scayt_autoStartup             = false;
		$config->scayt_contextCommands         = 'all';
		$config->scayt_maxSuggestions          = 5;
		$config->scayt_moreSuggestions         = 'off';
		$config->bodyClass                     = 'ckeditor-body';
		$config->contentsCss                   = array();
		$config->templates                     = array('hubzero');
		$config->templates_files               = array('/core/plugins/editors/ckeditor/assets/templates/hub.js');
		$config->templates_replaceContent      = false;
		$config->filebrowserBrowseUrl          = '';
		$config->filebrowserImageBrowseUrl     = '';
		$config->filebrowserImageBrowseLinkUrl = '';
		$config->filebrowserUploadUrl          = '';
		$config->filebrowserWindowWidth        = 400;
		$config->filebrowserWindowHeight       = 600;
		$config->toolbar                       = array(
			array('Find', 'Replace', '-', 'Scayt', 'Maximize', 'ShowBlocks'),
			array('Image','Table','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'HubzeroEquation', 'HubzeroGrid', 'Templates'),
			array('Link', 'Unlink', 'Anchor'),
			'/',
			array('Format', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'),
			array('NumberedList', 'BulletedList', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
			//array('HubzeroAutoGrow', 'HubzeroMacro')
			array('HubzeroMacro')
		);

		// if minimal toolbar
		if (in_array('minimal', $this->params->get('class')))
		{
			$config->toolbar                   = array(
				array('Link', 'Unlink', 'Anchor'),
				array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'),
				array('NumberedList', 'BulletedList')
			);
			$config->toolbarCanCollapse = false;

			// [QUBES][#561] SPW: always show resize, even in minimal mode
			//$config->resize_enabled = false;
			//$config->hubzeroAutogrow_autoStart = false;
		}

		// image plugin if in minimal mode
		if (in_array('minimal', $this->params->get('class')) && in_array('images', $this->params->get('class')))
		{
			// push after links section
			$config->toolbar = array_merge(array_splice($config->toolbar, 0, 1), array(array('Image')), $config->toolbar);
		}

		// macros popup
		if (in_array('macros', $this->params->get('class')))
		{
			$config->toolbar[] = array('HubzeroMacro');
		}

		// if no footer
		if (in_array('no-footer', $this->params->get('class')))
		{
			$config->removePlugins = 'elementspath';
		}

		// setup codemirror
		$config->codemirror                         = new stdClass;
		$config->codemirror->autoFormatOnModeChange = false;
		$config->codemirror->autoCloseTags          = false;
		$config->codemirror->autoCloseBrackets      = false;

		// startup mode
		if (in_array($this->params->get('startupMode'), array('wysiwyg','source')))
		{
			$config->startupMode = $this->params->get('startupMode');
		}

		// show source button
		if ($this->params->get('sourceViewButton'))
		{
			array_unshift($config->toolbar[0], 'Source', '-');
			$config->extraPlugins .= ',codemirror';
		}

		// height
		if ($this->params->get('height'))
		{
			$config->height = $this->params->get('height', '200px');
		}

		// // autogrow auto-start
		// if (is_bool($this->params->get('autoGrowAutoStart')))
		// {
		// 	$config->hubzeroAutogrow_autoStart = $this->params->get('autoGrowAutoStart');
		// }

		// // auto grow min height
		// if (is_numeric($this->params->get('autoGrowMinHeight')))
		// {
		// 	$config->hubzeroAutogrow_minHeight = $this->params->get('autoGrowMinHeight');
		// }

		// // autogrow max height
		// if (is_numeric($this->params->get('autoGrowMaxHeight')))
		// {
		// 	$config->hubzeroAutogrow_maxHeight = $this->params->get('autoGrowMaxHeight');
		// }

		// auto start spell check
		if (is_bool($this->params->get('spellCheckAutoStart')))
		{
			$config->scayt_autoStartup = $this->params->get('spellCheckAutoStart');
		}

		// spell check max suggesstions
		if (is_numeric($this->params->get('spellCheckMaxSuggesstions')))
		{
			$config->scayt_maxSuggestions = $this->params->get('spellCheckMaxSuggesstions');
		}

		// class to add to ckeditor body
		if ($this->params->get('contentBodyClass'))
		{
			$config->bodyClass = $this->params->get('contentBodyClass');
		}

		// add stylesheets to ckeditor content
		if (is_array($this->params->get('contentCss')) && count($this->params->get('contentCss')))
		{
			$config->contentsCss = $this->params->get('contentCss');
		}
		else
		{
			// always get front end template
			if (!$template)
			{
				$db = App::get('db');
				$db->setQuery("SELECT `template` FROM `#__template_styles` WHERE `client_id`='0' AND `home`='1'");
				$template = $db->loadResult();
			}

			$app = substr(PATH_APP, strlen(PATH_ROOT));

			// vars to hold css
			$css          = array();
			$siteCss      = $app . '/cache/site/site.css';
			$templateCssA = $app . '/templates/' . $template . '/css/main.css';
			$templateCssC = '/core/templates/' . $template . '/css/main.css';

			// do we have a site.css
			if (file_exists(PATH_APP . $siteCss))
			{
				array_push($css, $siteCss);
			}

			// do we have a template main.css
			if (file_exists(PATH_ROOT . $templateCssA))
			{
				array_push($css, $templateCssA);
			}
			else if (file_exists(PATH_ROOT . $templateCssC))
			{
				array_push($css, $templateCssC);
			}

			if (Document::getType() == 'html')
			{
				$head = Document::getHeadData();

				// add already added stylesheets
				foreach ($head['styleSheets'] as $sheet => $attribs)
				{
					array_push($css, $sheet);
				}
			}

			// set the content css
			$config->contentsCss = $css;
		}

		// file browsing
		if ($this->params->get('fileBrowserBrowseUrl'))
		{
			$config->filebrowserBrowseUrl = $this->params->get('fileBrowserBrowseUrl');
		}

		// image browsing
		if ($this->params->get('fileBrowserImageBrowseUrl'))
		{
			$config->filebrowserImageBrowseUrl = $this->params->get('fileBrowserImageBrowseUrl');
		}

		// file upload
		if ($this->params->get('fileBrowserUploadUrl'))
		{
			$config->filebrowserUploadUrl = $this->params->get('fileBrowserUploadUrl');
		}

		// file browse popup size
		if ($this->params->get('fileBrowserWindowWidth'))
		{
			$config->filebrowserWindowWidth = $this->params->get('fileBrowserWindowWidth');
		}
		if ($this->params->get('fileBrowserWindowHeight'))
		{
			$config->filebrowserWindowHeight = $this->params->get('fileBrowserWindowHeight');
		}

		// page templates
		if ($this->params->get('templates_files') && is_object($this->params->get('templates_files')))
		{
			foreach ($this->params->get('templates_files') as $name => $template)
			{
				//make sure templates exists
				if (file_exists(PATH_ROOT . $template))
				{
					// do we want to replace original ones
					if ($this->params->get('templates_replace'))
					{
						$config->templates = array();
						$config->templates_files = array();
					}

					array_push($config->templates, $name);
					array_push($config->templates_files, $template);
				}
			}
		}
		// make template definition a string
		$config->templates = implode(',', $config->templates);

		// allow scripts
		if ($this->params->get('allowScriptTags'))
		{
			$config->protectedSource[] = '/<script[^>]*>(.|\n)*<\/script>/ig';
		}

		// allow php
		if ($this->params->get('allowPhpTags'))
		{
			$config->protectedSource[] = '/<\?[\s\S]*?\?>/g';
			$config->codemirror->mode = 'application/x-httpd-php';
		}

		// set editor skin
		$config->skin = $this->params->get('skin', 'moono');

		if (User::authorise('core.manage', 'com_system'))
		{
			$config->allowedContent = true;
		}

		return $config;
	}
}
