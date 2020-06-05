<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

Html::behavior('core');

/**
 * CKEditor Plugin
 */
class plgEditorCkeditor5 extends \Hubzero\Plugin\Plugin
{
	/**
	 * Base path for editor files
	 */
	protected $_basePath = 'core/plugins/editors/ckeditor5/assets/';

	/**
	 * Method to handle the onInitEditor event.
	 *  - Initialises the Editor
	 *
	 * @return  string JavaScript Initialization string
	 */
	public function onInit()
	{
		// Add ckeditor
		Document::addScript(str_replace('/administrator', '', Request::base(true)) . '/' . $this->_basePath . 'js/ckeditor.js' );
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
	}

	/**
	 * Get the editor content.
	 * 
	 * Not applicable in this editor
	 *
	 * @param   string $id The id of the editor field.
	 * @return  string
	 */
	public function onGetContent($id)
	{
		return "";
	}

	/**
	 * Set the editor content.
	 * 
	 * Not applicable in this editor
	 *
	 * @param   string $id   The id of the editor field.
	 * @param   string $html The content to set.
	 * @return  string
	 */
	public function onSetContent($id, $html)
	{
		return "";
	}

	/**
	 * Inserts text
	 * 
	 * Not applicable in this editor
	 *
	 * @param	string	$id
	 * @return	string
	 */
	public function onGetInsertMethod($id)
	{
		return "";
	}

	/**
	 * Display the editor area.
	 *
	 * @param   string   $name     The control name.
	 * @param   string   $content  The contents of the text area.
	 * @param   string   $width    The width of the text area (px or %).
	 * @param   string   $height   The height of the text area (px or %).
	 * @param   int      $col      The number of columns for the textarea.
	 * @param   int      $row      The number of rows for the textarea.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string   $asset
	 * @param   object   $author
	 * @param   array    $params  Associative array of editor parameters.
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		// Make sure we have an id too
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
		$params['class'][] = 'ckeditor-content';

		// Set default height to a rough approximation of the height
		// of the textarea (rows * 1.5em of 12px font)
		if (!isset($params['height']))
		{
			$params['height'] = intval($row) . 'em';
		}

		// Fix script and php protected source
		$config = "{ }";

		// Script to actually make ckeditor
		$script = '<script type="text/javascript">';
		$script .= 'ClassicEditor';
		$script .= '.create(document.querySelector("#'.$id.'"), '.$config.')';
		$script .= '.catch( error => { console.error( error ); } );';
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

		// Couldn't find a better way to do this for the timebeing - CK5 documentation is lacking at the moment
		// Puts the style in multiple times and overwrites when there are multiple textareas
		$this->css('.ck-content { height: ' . $row . 'em; }');

		// Output html and script
		$editor  = '<textarea name="' . $name . '" id="' . $id . '" '. implode(' ', $atts) . '>' . $content . '</textarea>' . $script;
		if (App::isAdmin())
		{
			$editor .= $this->_displayButtons($id, $buttons, $asset, $author);
		}
		return $editor;
	}

	/**
	 * Displays buttons
	 *
	 * @param   string  $name
	 * @param   array   $buttons
	 * @param   string  $asset
	 * @param   string  $string
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
					$onclick = ($button->get('onclick')) ? ' onclick="'.$button->get('onclick').'"' : 'onclick="return false;"';
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
}
