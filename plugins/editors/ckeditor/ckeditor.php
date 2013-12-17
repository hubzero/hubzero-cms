<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Plain Textarea Editor Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors.none
 * @since		1.5
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
	 * @return	string	JavaScript Initialization string
	 * @since 1.5
	 */
	public function onInit()
	{
		// add ckeditor
		$document = JFactory::getDocument();
		$document->addScript( $this->_basePath . 'ckeditor.js' );
		$document->addScript( $this->_basePath . '/adapters/jquery.js' );
	}

	/**
	 * Copy editor content to form field.
	 *
	 * Not applicable in this editor.
	 *
	 * @return	void
	 */
	function onSave()
	{
		return;
	}

	/**
	 * Get the editor content.
	 *
	 * @param	string	$id		The id of the editor field.
	 *
	 * @return	string
	 */
	function onGetContent($id)
	{
		return "document.getElementById('$id').value;\n";
	}

	/**
	 * Set the editor content.
	 *
	 * @param	string	$id		The id of the editor field.
	 * @param	string	$html	The content to set.
	 *
	 * @return	string
	 */
	function onSetContent($id, $html)
	{
		return "document.getElementById('$id').value = $html;\n";
	}

	/**
	 * @param	string	$id
	 *
	 * @return	string
	 */
	function onGetInsertMethod($id)
	{
		return true;
	}

	/**
	 * Display the editor area.
	 *
	 * @param	string	$name		The control name.
	 * @param	string	$html		The contents of the text area.
	 * @param	string	$width		The width of the text area (px or %).
	 * @param	string	$height		The height of the text area (px or %).
	 * @param	int		$col		The number of columns for the textarea.
	 * @param	int		$row		The number of rows for the textarea.
	 * @param	boolean	$buttons	True and the editor buttons will be displayed.
	 * @param	string	$id			An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param	string	$asset
	 * @param	object	$author
	 * @param	array	$params		Associative array of editor parameters.
	 *
	 * @return	string
	 */
	function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		//make sure we have an id too
		if (empty($id)) 
		{
			$id = $name;
		}
		
		// build config & json encode
		$config = json_encode($this->_buildConfig());
		
		// fix script and php protected source
		
		//$config = str_replace('"\\/<group:include([^\\/]*)\\/>\\/g"', '/<group:include([^/]*)/>/g', $config);
		$config = str_replace('"\\/<script[^>]*>(.|\\\\n)*<\\\\\\/script>\\/ig"', '/<script[^>]*>(.|\n)*<\/script>/ig', $config);
		$config = str_replace('"\\/<\\\\?[\\\\s\\\\S]*?\\\\?>\\/g"', '/<\?[\s\S]*?\?>/g', $config);
		$config = str_replace('"\/<group:include([^\\\\\/]*)\\\\\/>\/g"', '/<group:include([^\\/]*)\\/>/g', $config);
		
		// script to actually make ckeditor
		$script = '<script>$(document).ready(function(){ $("#'.$id.'").ckeditor(function(){}, '.$config.'); });</script>';
		
		// output html and script
		$editor  = "<textarea name=\"$name\" id=\"$id\">$content</textarea>" . $script;
		return $editor;
	}
	
	private function _buildConfig()
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
		$config->extraPlugins                  = 'tableresize,hubzeroautogrow,hubzeroequation,hubzerogrid,codemirror';
		$config->removePlugins                 = 'resize';
		$config->resize_enabled                = false;
		$config->emailProtection               = 'encode';
		$config->protectedSource               = array('/<group:include([^\\/]*)\\/>/g');
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
			array('Save', 'Templates'),
			array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
			array('Find', 'Replace', '-', 'Scayt'),
			'/',
			array('Format', 'FontSize'),
			array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'),
			array('NumberedList', 'BulletedList', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
			'/',
			array('Image','Table','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'HubzeroEquation', 'HubzeroGrid'),
			array('Link', 'Unlink', 'Anchor'),
			array('Maximize', 'ShowBlocks'),
			array('HubzeroAutoGrow')
		);
		
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
		}
		
		// source syntax highlighting - using codemirror
		if ($params->get('sourceViewSyntaxHighlighing') === false)
		{
			$config->extraPlugins = str_replace(',codemirror', '', $config->extraPlugins);
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
