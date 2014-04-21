<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

/**
 * CKEditor Plugin
 */
class plgEditorCkeditor extends JPlugin
{
	/**
	 * Base path for editor files
	 */
	protected $_basePath = 'media/editors/ckeditor/';

	/**
	 * Method to handle the onInitEditor event.
	 *  - Initialises the Editor
	 *
	 * @return  string JavaScript Initialization string
	 */
	public function onInit()
	{
		// add ckeditor stylesheet
		Hubzero\Document\Assets::addPluginStylesheet('editors', 'ckeditor');

		// add ckeditor
		$document = JFactory::getDocument();
		$document->addScript(str_replace('/administrator', '', JURI::base(true)) . '/' . $this->_basePath . 'ckeditor.js' );
		$document->addScript(str_replace('/administrator', '', JURI::base(true)) . '/' . $this->_basePath . 'adapters/jquery.js' );
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
		return;
	}

	/**
	 * Get the editor content.
	 *
	 * @param   string $id The id of the editor field.
	 * @return  string
	 */
	public function onGetContent($id)
	{
		return "document.getElementById('$id').value;\n";
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
		return "document.getElementById('$id').value = $html;\n";
	}

	/**
	 * @param	string	$id
	 * @return	string
	 */
	public function onGetInsertMethod($id)
	{
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
			foreach ($this->_split(' ', $cls) as $piece)
			{
				$params['class'][] = $piece;
			}
		}
		$params['class'][] = 'ckeditor-content';
		
		// build config & json encode
		$config = json_encode($this->_buildConfig($params['class']));

		// fix script and php protected source
		//$config = str_replace('"\\/<group:include([^\\/]*)\\/>\\/g"', '/<group:include([^/]*)/>/g', $config);
		$config = str_replace('"\\/<script[^>]*>(.|\\\\n)*<\\\\\\/script>\\/ig"', '/<script[^>]*>(.|\n)*<\/script>/ig', $config);
		$config = str_replace('"\\/<\\\\?[\\\\s\\\\S]*?\\\\?>\\/g"', '/<\?[\s\S]*?\?>/g', $config);
		$config = str_replace('"\/<group:include([^\\\\\/]*)\\\\\/>\/g"', '/<group:include([^\\/]*)\\/>/g', $config);

		// script to actually make ckeditor
		$script  = '<script type="text/javascript">';
		$script .= 'if (typeof(jQuery) !== "undefined") {';
		$script .= 'jQuery(document).ready(function(){ jQuery("#'.$id.'").ckeditor(function(){}, '.$config.'); });';
		$script .= 'jQuery(document).on("ajaxLoad", function() { jQuery("#'.$id.'").ckeditor(function(){}, '.$config.'); });';
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
		if (JFactory::getApplication()->isAdmin())
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
		JHtml::_('behavior.modal', 'a.modal-button');

		$args['name'] = $name;
		$args['event'] = 'onGetInsertMethod';

		$return = '';
		$results[] = $this->update($args);

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

			/*
			 * This will allow plugins to attach buttons or change the behavior on the fly using AJAX
			 */
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";

			foreach ($results as $button)
			{
				/*
				 * Results should be an object
				 */
				if ( $button->get('name') ) {
					$modal		= ($button->get('modal')) ? ' class="modal-button"' : null;
					$href		= ($button->get('link')) ? ' href="'.JURI::base().$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? ' onclick="'.$button->get('onclick').'"' : 'onclick="IeCursorFix(); return false;"';
					$title      = ($button->get('title')) ? $button->get('title') : $button->get('text');
					$return .= '<div class="button2-left"><div class="' . $button->get('name')
						. '"><a' . $modal . ' title="' . $title . '"' . $href . $onclick . ' rel="' . $button->get('options')
						. '">' . $button->get('text') . "</a></div></div>\n";
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
	private function _buildConfig($classes = array())
	{
		// store params in local var for easier accessing
		$params = $this->params;

		// object to hold our final config
		$config                                = new stdClass;
		$config->startupMode                   = 'wysiwyg';
		$config->tabSpaces                     = 4;
		$config->hubzeroAutogrow_autoStart     = true;
		$config->hubzeroAutogrow_minHeight     = 200;
		$config->hubzeroAutogrow_maxHeight     = 1000;
		$config->toolbarCanCollapse            = true;
		$config->extraPlugins                  = 'tableresize,iframedialog,hubzeroautogrow,hubzeroequation,hubzerogrid,hubzeromacro,hubzerohighlight';
		$config->removePlugins                 = 'resize';
		$config->resize_enabled                = false;
		$config->emailProtection               = 'encode';
		$config->protectedSource               = array('/<group:include([^\\/]*)\\/>/g');
		$config->extraAllowedContent           = 'mark(*)[*]';
		$config->disableNativeSpellChecker     = false;
		$config->scayt_autoStartup             = true;
		$config->scayt_contextCommands         = 'all';
		$config->scayt_maxSuggestions          = 5;
		$config->scayt_moreSuggestions         = 'off';
		$config->bodyClass                     = 'ckeditor-body';
		$config->contentsCss                   = array();
		$config->templates                     = array('hubzero');
		$config->templates_files               = array('/media/editors/ckeditor/templates/hub.js');
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
			array('HubzeroAutoGrow', 'HubzeroMacro')
		);

		// if minimal toolbar
		if (in_array('minimal', $classes))
		{
			$config->toolbar                   = array(
				array('Link', 'Unlink', 'Anchor'),
				array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'),
				array('NumberedList', 'BulletedList')
			);
			$config->toolbarCanCollapse = false;
			$config->hubzeroAutogrow_autoStart = false;
		}

		// macros popup
		if (in_array('macros', $classes))
		{
			$config->toolbar[] = array('HubzeroMacro');
		}

		// if no footer
		if (in_array('no-footer', $classes))
		{
			$config->removePlugins = 'elementspath';
		}

		// setup codemirror
		$config->codemirror = new stdClass;
		$config->codemirror->autoFormatOnModeChange = false;

		// startup mode
		if (in_array($params->get('startupMode'), array('wysiwyg','source')))
		{
			$config->startupMode = $params->get('startupMode');
		}

		// show source button
		if ($params->get('sourceViewButton'))
		{
			array_unshift($config->toolbar[0], 'Source', '-');
			$config->extraPlugins .= ',codemirror';
		}

		// autogrow auto-start
		if (is_bool($params->get('autoGrowAutoStart')))
		{
			$config->hubzeroAutogrow_autoStart = $params->get('autoGrowAutoStart');
		}

		// auto grow min height
		if (is_numeric($params->get('autoGrowMinHeight')))
		{
			$config->hubzeroAutogrow_minHeight = $params->get('autoGrowMinHeight');
		}

		// autogrow max height
		if (is_numeric($params->get('autoGrowMaxHeight')))
		{
			$config->hubzeroAutogrow_maxHeight = $params->get('autoGrowMaxHeight');
		}

		// auto start spell check
		if (is_bool($params->get('spellCheckAutoStart')))
		{
			$config->scayt_autoStartup = $params->get('spellCheckAutoStart');
		}

		// spell check max suggesstions 
		if (is_numeric($params->get('spellCheckMaxSuggesstions')))
		{
			$config->scayt_maxSuggestions = $params->get('spellCheckMaxSuggesstions');
		}

		// class to add to ckeditor body
		if ($params->get('contentBodyClass'))
		{
			$config->bodyClass = $params->get('contentBodyClass');
		}

		// add stylesheets to ckeditor content
		if (is_array($params->get('contentCss')) && count($params->get('contentCss')))
		{
			$config->contentsCss = $params->get('contentCss');
		}
		else
		{
			// database & document objects
			$db  = JFactory::getDBO();
			$doc = JFactory::getDocument();

			// always get front end template
			$db->setQuery("SELECT `template` FROM `#__template_styles` WHERE `client_id`='0' AND `home`='1'");
			$template = $db->loadResult();

			// vars to hold css	
			$css         = array();
			$siteCss     = '/cache/site.css';
			$templateCss = '/templates/' . $template . '/css/main.css';
			
			// do we have a site.css
			if (file_exists(JPATH_ROOT . $siteCss))
			{
				array_push($css, $siteCss);
			}

			// do we have a template main.css
			if (file_exists(JPATH_ROOT . $templateCss))
			{
				array_push($css, $templateCss);
			}
			
			// add already added stylesheets
			foreach ($doc->_styleSheets as $sheet => $attribs)
			{
				array_push($css, $sheet);
			}

			// set the content css
			$config->contentsCss = $css;
		}

		// file browsing
		if ($params->get('fileBrowserBrowseUrl'))
		{
			$config->filebrowserBrowseUrl = $params->get('fileBrowserBrowseUrl');
		}

		// image browsing
		if ($params->get('fileBrowserImageBrowseUrl'))
		{
			$config->filebrowserImageBrowseUrl = $params->get('fileBrowserImageBrowseUrl');
		}

		// file upload
		if ($params->get('fileBrowserUploadUrl'))
		{
			$config->filebrowserUploadUrl = $params->get('fileBrowserUploadUrl');
		}

		// file browse popup size
		if ($params->get('fileBrowserWindowWidth'))
		{
			$config->filebrowserWindowWidth = $params->get('fileBrowserWindowWidth');
		}
		if ($params->get('fileBrowserWindowHeight'))
		{
			$config->filebrowserWindowHeight = $params->get('fileBrowserWindowHeight');
		}

		// page templates
		if ($params->get('templates_files') && is_object($params->get('templates_files')))
		{
			foreach ($params->get('templates_files') as $name => $template)
			{
				//make sure templates exists
				if (file_exists(JPATH_ROOT . $template))
				{
					// do we want to replace original ones
					if ($params->get('templates_replace'))
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
		if ($params->get('allowScriptTags'))
		{
			$config->protectedSource[] = '/<script[^>]*>(.|\n)*<\/script>/ig';
		}

		// allow php
		if ($params->get('allowPhpTags'))
		{
			$config->protectedSource[] = '/<\?[\s\S]*?\?>/g';
			$config->codemirror->mode = 'application/x-httpd-php';
		}

		return $config;
	}
}
